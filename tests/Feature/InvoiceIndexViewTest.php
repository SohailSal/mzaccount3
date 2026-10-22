<?php

namespace Tests\Feature;

use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class InvoiceIndexViewTest extends TestCase
{
    public function test_invoice_index_renders_without_the_intl_extension()
    {
        $response = $this->view('invoices.index', [
            'invoices' => new LengthAwarePaginator([], 30, 10),
            'search' => null,
        ]);

        $response->assertSee('Invoices')
            ->assertSee('class="pagination"', false)
            ->assertDontSee('<svg', false);
    }
}
