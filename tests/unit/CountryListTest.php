<?php

use App\Libraries\CountryList;
use CodeIgniter\Test\CIUnitTestCase;

final class CountryListTest extends CIUnitTestCase
{
    public function testEmptyCountryTableStillProvidesRegistrationOptions(): void
    {
        $rows = CountryList::forDropdown([]);
        $names = array_column($rows, 'country_name');

        $this->assertCount(249, $names);
        $this->assertContains('Nigeria', $names);
        $this->assertContains('United Kingdom', $names);
        $this->assertContains('United States', $names);
        $this->assertNotContains('', $names);
    }

    public function testUnusableRowsAlsoUseTheFallback(): void
    {
        $this->assertSame(
            CountryList::forDropdown([]),
            CountryList::forDropdown([[], ['country_name' => null], ['country_name' => '   ']]),
        );
    }

    public function testDatabaseNamesRemainTheSubmittedValues(): void
    {
        $this->assertSame([
            ['country_name' => 'Canada'],
            ['country_name' => 'Nigeria'],
        ], CountryList::forDropdown([
            ['country_name' => ' Nigeria '],
            ['country_name' => 'Canada'],
            ['country_name' => 'Nigeria'],
            ['country_name' => ''],
        ]));
    }

    public function testFunnelRendersFallbackAsSelectableCountryNames(): void
    {
        $html = module_view('Web', 'emergence_funnel', [
            'countries' => CountryList::forDropdown([]),
            'apiEndpoint' => '/emergence/funnel/register',
            'checkoutUrl' => '/emergence/checkout',
            'portalUrl' => '/attendees/login',
            'upsellPrice' => '$50',
            'mailchimpTags' => '',
        ]);
        $document = new DOMDocument();
        @$document->loadHTML($html);
        $xpath = new DOMXPath($document);

        $this->assertSame(250, $xpath->query('//select[@name="country"]/option')->length);
        $this->assertSame(1, $xpath->query('//select[@name="country"]/option[@value="Nigeria"]')->length);
        $this->assertSame(0, $xpath->query('//select[@name="country"][@disabled]')->length);
    }
}
