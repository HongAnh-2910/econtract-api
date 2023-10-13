<?php

    namespace App\Services;

    use Goutte\Client;

    class CrawlCompanyInformation
    {
        protected Client $client;
        public function __construct(Client $client)
        {
            $this->client = $client;
        }

        /**
         * @param $taxCode
         * @return array
         */

        public function run($taxCode)
        {
            $crawlCompanyInformation = [];
            $crawler = $this->client->request('GET' ,'https://masocongty.vn/search?name='.trim($taxCode));
            $crawler->filter('h3')->each(function ($node) use (&$crawlCompanyInformation) {
                $crawlCompanyInformation['company_name'] = $node->text();
            });
            $crawler->filter('ul#detail-list li:nth-child(1) > span')->each(function ($node) use (&$crawlCompanyInformation) {
                $crawlCompanyInformation['address'] = $node->text();
            });

            $crawler->filter('ul#detail-list li:nth-child(3) > strong')->each(function ($node) use (&$crawlCompanyInformation) {
                $crawlCompanyInformation['ceo_name'] = $node->text();
            });
            return $crawlCompanyInformation;
        }
    }
