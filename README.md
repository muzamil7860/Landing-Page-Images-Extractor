# Landing-Page-Images-Extractor
A PHP-based web scraping tool for extracting headings and associated image dimensions from landing pages. Supports both single-page and full-site scans. Export results in a clean text report. Built for marketing and SEO audits.


## Overview

CBS Landing Page Scraper is a simple PHP-based web scraping tool designed to extract headings and associated image dimensions from a website's landing page(s). This tool is ideal for SEO audits, content analysis, and website marketing. The scraper extracts various heading tags (`<h1>`, `<h2>`, `<h3>`) and the nearest images, reporting their sizes (width and height).

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Getting Started](#getting-started)
  - [Prerequisites](#prerequisites)
  - [Installation](#installation)
  - [Running the Application](#running-the-application)
- [Usage](#usage)
- [Output](#output)
- [Filters](#filters)
- [Error Handling](#error-handling)
- [File Structure](#file-structure)
- [Contributing](#contributing)
- [License](#license)
- [Author](#author)

---

## Features

- ✅ **Extract Heading Tags**: Scrapes `<h1>`, `<h2>`, and `<h3>` headings from the landing page.
- ✅ **Image Detection**: Detects the nearest image to each heading and reports its dimensions (width and height).
- ✅ **Full-Site Scraping**: Optionally scrape all internal links within the site.
- ✅ **Excludes Irrelevant Sections**: Skips headings and links related to standard pages like `Contact`, `FAQ`, `Privacy Policy`, etc.
- ✅ **Reports Generation**: Provides downloadable text-based reports with heading and image size details.
- ✅ **User-Friendly Interface**: Simple web-based UI for interaction.
- ✅ **Portable**: Can be run locally via PHP server.

---

## Tech Stack

- **PHP** (for backend processing and web scraping)
- **DOMDocument** (for parsing HTML)
- **XPath** (for querying HTML elements)
- **HTML/CSS** (for frontend presentation)
- **JavaScript** (for basic interactivity)
