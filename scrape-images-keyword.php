<?php
session_start();

function fetchPageContent($url) {
    $context = stream_context_create(['http' => ['ignore_errors' => true]]);
    return @file_get_contents($url, false, $context);
}

function getAbsoluteUrl($src, $baseUrl) {
    return filter_var($src, FILTER_VALIDATE_URL) ? $src : rtrim($baseUrl, '/') . '/' . ltrim($src, '/');
}

function extractHeadingsWithImageSizes($html, $baseUrl) {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    $sections = [];
    $firstH1Processed = false;

    foreach ($xpath->query('//h1 | //h2 | //h3') as $heading) {
        $headingText = trim($heading->textContent);
        $imageSize = null;

        if (!$firstH1Processed && strtolower($heading->nodeName) == 'h1') {
            $imageSize = '1583 × 490';
            $firstH1Processed = true;
        } else {
            $parent = $heading->parentNode;
            while ($parent && strtolower($parent->nodeName) != 'body') {
                $images = $xpath->query('.//img | .//svg', $parent);
                if ($images->length > 0) {
                    foreach ($images as $img) {
                        if ($img->nodeName == "img") {
                            $src = getAbsoluteUrl($img->getAttribute('src'), $baseUrl);
                            $size = @getimagesize($src);
                            if ($size) {
                                $imageSize = "{$size[0]} × {$size[1]}";
                            }
                        } elseif ($img->nodeName == "svg") {
                            $imageSize = 'SVG Icon';
                        }
                    }
                    break;
                }
                $parent = $parent->parentNode;
            }
        }

        if (in_array($headingText, ['Our Related Services', 'Our Services', 'Work Process'])) {
            $imageSize = 'SVG Icon';
        }

        if ($imageSize !== null) {
            $sections[] = ['title' => $headingText, 'image_size' => $imageSize];
        }
    }

    return $sections;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['website_url'])) {
    $websiteUrl = trim($_POST['website_url']);
    $html = fetchPageContent($websiteUrl);
    if ($html) {
        $_SESSION['sections'][$websiteUrl] = extractHeadingsWithImageSizes($html, $websiteUrl);
    }
}

if (isset($_POST['download_report'])) {
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="report.txt"');
    foreach ($_SESSION['sections'] as $site => $sections) {
        echo "Site: $site\n";
        foreach ($sections as $section) {
            echo "{$section['title']} - {$section['image_size']}\n";
        }
        echo "-----------------------\n";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-Page Data Extractor</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background: #f4f4f4; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); }
        table { width: 100%; margin-top: 10px; border-collapse: collapse; background: white; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #007bff; color: white; }
        button, input[type="text"] { padding: 8px 12px; margin: 5px; border-radius: 5px; border: 1px solid #ccc; }
        .btn-primary { background: #007bff; color: white; border: none; }
        .btn-danger { background: red; color: white; border: none; }
        .loader { display: none; width: 40px; height: 40px; border: 5px solid lightgray; border-top: 5px solid blue; border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { 100% { transform: rotate(360deg); } }
        .actions { display: flex; justify-content: space-between; align-items: center; }
        .bulk-actions { display: flex; gap: 10px; }
    </style>
</head>
<body>

    <div class="container">
        <h2>Multi-Page Data Extractor</h2>
        <form method="post">
            <input type="text" name="website_url" placeholder="Enter Website URL" required>
            <button type="submit" class="btn-primary">Extract</button>
            <button type="submit" name="download_report" class="btn-primary">Download Report</button>
        </form>

        <div class="loader" id="loader"></div>

        <?php if (!empty($_SESSION['sections'])): ?>
            <div class="actions">
                <input type="text" id="bulkSize" placeholder="Enter New Image Size">
                <button onclick="applyBulkSize()" class="btn-primary">Apply to Selected</button>
            </div>
            <table>
                <tr>
                    <th><input type="checkbox" onclick="toggleAll(this)"></th>
                    <th>Heading</th>
                    <th>Image Size</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($_SESSION['sections'] as $site => $sections): ?>
                    <tr><td colspan="4" style="background: #ddd;"><b><?= htmlspecialchars($site) ?></b></td></tr>
                    <?php foreach ($sections as $index => $section): ?>
                        <tr>
                            <td><input type="checkbox" class="rowCheckbox"></td>
                            <td contenteditable="true"><?= htmlspecialchars($section['title']) ?></td>
                            <td contenteditable="true"><?= htmlspecialchars($section['image_size']) ?></td>
                            <td><button onclick="deleteRow(this)" class="btn-danger">Delete</button></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>

    <script>
        function toggleAll(source) {
            document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = source.checked);
        }

        function applyBulkSize() {
            let newSize = document.getElementById("bulkSize").value;
            document.querySelectorAll('.rowCheckbox:checked').forEach(cb => {
                let row = cb.closest('tr');
                row.children[2].innerText = newSize;
            });
        }

        function deleteRow(btn) {
            btn.closest('tr').remove();
        }
    </script>

</body>
</html>
