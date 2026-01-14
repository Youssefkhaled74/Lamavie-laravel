PDF Setup and Arabic (RTL) support

Recommended package: `baidouabdellah/laravel-arpdf`

1) Install the package (run locally):

```bash
composer require baidouabdellah/laravel-arpdf
```

2) If the package publishes provider/config, follow its docs (usually):

```bash
php artisan vendor:publish --provider="BaidouAbdellah\ArPdf\ArPdfServiceProvider"
```

3) If you prefer mPDF instead, install it:

```bash
composer require mpdf/mpdf
```

4) DomPDF fallback (if you can't use mPDF):

```bash
composer require barryvdh/laravel-dompdf
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

5) Arabic fonts

- For mPDF: copy .ttf font files (e.g., Noto Naskh Arabic, Amiri) to `storage/fonts` and configure mPDF font data per mPDF docs.
- For DomPDF: add fonts to `config/dompdf.php` (`font_dir`, `font_data`) and ensure `CPDF_ENABLE_HTML5PARSER` is enabled if needed.

6) Usage (what this repo does):

- The controller `BookingController::invoicePdf()` will:
  - Prefer the `arpdf` container binding (app('arpdf')) or `ArPdf` facade if available.
  - Then try `mpdf/mpdf`.
  - Then try `Gpdf`.
  - Then try `barryvdh/laravel-dompdf` as a final fallback.

7) Testing

- After installing a PDF library, open a booking in the admin area, click "Invoice", choose a status, then click "Download PDF".
- If PDFs render with garbled Arabic, ensure correct Arabic font is configured and used by the library.

If you'd like, I can add an example font registration snippet for mPDF or DomPDF in this repo next.