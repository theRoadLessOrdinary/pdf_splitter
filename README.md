# pdf_splitter (PDF Tiler)

Splits a large single-page PDF (sewing patterns, plans, posters) into US Letter pages you can print at home and tape together. Each output page gets crop marks, a "Page N of M  column:row" label, and an optional 1" or 2" grid for lining pages up and checking printer accuracy.

Drop a PDF on the page (or click to browse). It uploads, then the tiled PDF opens below.

## Requirements

- PHP 8.x on Apache (`.htaccess` blocks direct web access to `todo/`, `usage.txt`, `cls/`, `vendor/` and `tpl/`)
- `todo/` and `usage.txt` writable by the web server

FPDF 1.82 and FPDI 2.6.1 are bundled in `vendor/`, so the folder is self-contained and can be dropped into any site.

## Limits

- Input must be an unencrypted PDF, version 1.4 or lower. FPDI's free parser can't read the compressed object streams newer PDFs often use.
- Uploads are capped at 5 MB.
- Only page 1 of the source is tiled.

## Files

| Path | What it is |
|---|---|
| `index.php` | Page, upload handler (`?cmd=upload`) and splitter (`?cmd=split&src=<file>&grid=<0,1,2>`) |
| `cls/splitter.class.php` | FPDI subclass that draws crop marks and the grid |
| `tpl/main.template.html` | The page |
| `js/pdf_split.js` | Dropzone setup |
| `todo/` | Uploaded PDFs (not in the repo) |
| `usage.txt` | Visitor log: time, IP, city/country, user agent (not in the repo) |

## Credits

By Bill in KCMO. Uses [FPDF](http://www.fpdf.org/), [FPDI](https://www.setasign.com/fpdi) (MIT) and [Dropzone](https://www.dropzone.dev/) (MIT).
