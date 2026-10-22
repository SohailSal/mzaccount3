<?php

namespace Tests\Unit;

use App\Support\PdfFilename;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderUtils;

class PdfFilenameTest extends TestCase
{
    public function test_generated_pdf_filename_does_not_contain_path_separators()
    {
        $filename = PdfFilename::sanitize('PV/2026\\05/001').'.pdf';

        $this->assertSame('PV-2026-05-001.pdf', $filename);
        $this->assertNotEmpty(HeaderUtils::makeDisposition('inline', $filename));
    }
}
