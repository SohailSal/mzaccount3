<?php

namespace Tests\Feature;

use Tests\TestCase;

class PdfRenderingTest extends TestCase
{
    public function test_pdf_page_number_footer_renders_with_dompdf_3()
    {
        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option('enable_php', true);
        $pdf->loadHTML(<<<'HTML'
            <html>
                <body>
                    <script type="text/php">
                        if (isset($pdf)) {
                            $pdf->page_text(500, 800, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, [0, 0, 0], 0.0, 0.0, 0.0);
                        }
                    </script>
                    PDF test
                </body>
            </html>
            HTML);

        $this->assertStringStartsWith('%PDF-', $pdf->output());
    }
}
