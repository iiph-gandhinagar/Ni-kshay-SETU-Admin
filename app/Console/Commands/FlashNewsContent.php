<?php

namespace App\Console\Commands;

use App\Models\FlashNews;
use Illuminate\Console\Command;
use Symfony\Component\HttpClient\HttpClient;
use Goutte\Client;
use Log;

class FlashNewsContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flash-news:content';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->line("Flash News Website Content Start!!!");
        $client = new Client(HttpClient::create(array(
            'headers' => array(
                'Cookie' => '__cfruid=50d1756983e6105f98ddb357cdf9059a2d747a2c-1666933110',
                'Cache-Control' => 'no-cache',
                'Host' => 'nikshay.zendesk.com',
                'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36', // will be forced using 'Symfony BrowserKit' in executing
                'Postman-Token' => '7603f851e0c085c6-BOM',
                // 'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                // 'Accept-Encoding' => 'gzip, deflate, br',
                // 'Accept-Language' => 'en-US,en;q=0.9',
                // 'Connection' => 'keep-alive',
                // 'Origin' => 'https://nikshay.zendesk.com/hc/en-us#360002770891',
                // 'Referer' => 'https://nikshay.zendesk.com/hc/en-us#360002770891/',
                // 'Content-Type' => 'application/json',
                // 'sec-ch-ua-mobile' => '?0',
                // 'sec-ch-ua-platform' => "Linux",
            ),
        )));

        $crawler = $client->request('GET', 'https://tbcindia.gov.in/');
        // Log::info($crawler->text());
        $crawler->filter('.newsmarquee .text12 .sublink2')->each(function ($node) {
            $data = collect([]);
            $data->push(['data' => $node->text(), 'link' => $node->filter('a')->attr('href')]);
            // Log::info($data);
            foreach ($data as $heading) {
                // Log::info($heading);
                $title = explode("( Release Date :", $heading['data']);
                // Log::info("title ---->".$title[0]);
                $news_exisit = FlashNews::where('title', 'LIKE', '%' . $title[0] . '%')->get();
                // Log::info($news_exisit);
                if (count($news_exisit) == 0) {
                    $newRequest['title'] = $title[0];
                    $newRequest['href'] = "https://tbcindia.gov.in/" . $heading['link'];
                    $newRequest['source'] = "https://tbcindia.gov.in/";
                    $newRequest['publish_date'] = str_replace(array(' )'), '', $title[1]);
                    FlashNews::create($newRequest);
                    // print $heading['data']."\n";
                }
            }
        });

        $crawler = $client->request('GET', 'https://nikshay.zendesk.com/hc/en-us/categories/360002770891-New-Releases');

        Log::info("second website--------------------------------------------------------------------------->");
        // Log::info($crawler->text());
        // $link = $client->click($crawler->selectLink('New Releases')->link());
        // Log::info($link);
        // $crawler = $client->click($link);
        $crawler->filter('.category-container .category-content .section-tree')->each(function ($node) {
            Log::info("data inside second website call");
            $data = collect([]);
            $data->push(['data' => $node->text(), 'link' => $node->filter('a')->attr('href')]);
            // Log::info($data);
            foreach ($data as $heading) {
                // Log::info($heading);
                $title = explode("( Release Date :", $heading['data']);
                $news_exisit = FlashNews::where('title', 'LIKE', '%' . $title[0] . '%')->get();
                if (count($news_exisit) == 0) {
                    $newRequest['title'] = $title[0];
                    $newRequest['href'] = "https://nikshay.zendesk.com/hc/en-us/categories/360002770891-New-Releases/" . $heading['link'];
                    $newRequest['source'] = "https://nikshay.zendesk.com/hc/en-us/categories/360002770891-New-Releases";
                    $newRequest['publish_date'] = str_replace(array(' )'), '', $title[1]);
                    FlashNews::create($newRequest);
                    // print $heading['data']."\n";
                }
            }
        });

        // $new_release = \Http::withHeaders([
        //         'Cookie' => '__cfruid=50d1756983e6105f98ddb357cdf9059a2d747a2c-1666933110',
        //         'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36', // will be forced using 'Symfony BrowserKit' in executing
        //         'Accept' => '*/*',
        //         'Accept-Encoding' => 'gzip, deflate, br',
        //         'Accept-Language' => 'en-US,en;q=0.9',
        //         'Cache-Control' => 'no-cache',
        //         'Connection' => 'keep-alive',
        //         'Origin' => 'https://nikshay.zendesk.com/hc/en-us#360002770891',
        //         'Referer' => 'https://nikshay.zendesk.com/hc/en-us#360002770891/',
        //         'Content-Type' => 'application/json',
        //         'Host' => 'nikshay.zendesk.com',
        // ])->get("https://nikshay.zendesk.com/hc/en-us#360002770891");
        // Log::info($new_release);
        // $final_json_release = json_decode($new_release->getBody()->getContents(), true);
        // Log::info($final_json_release);
        // $who_website_content = $final_json_release['modules'][0]['submodules'][1]['recommendations'];
        // foreach ($who_website_content as $content) {
        //     // Log::info("inside content");
        //     // Log::info($content);
        //     // Log::info($content['recommendation']);
        //     // Log::info($content['population'][0]['description']);
        //     $newRequest['title'] = $content['population'][0]['description'];
        //     $newRequest['description'] = $content['recommendation'];
        //     $newRequest['href'] = "https://who.tuberculosis.recmap.org/recommendation/" . $content['@id'];
        //     $newRequest['source'] = "https://tbksp.org/en/recommendation/page-1";
        //     // FlashNews::create($newRequest);
        // }

        $crawler = $client->request('GET', 'https://www.who.int/publications/i/item/9789240037021');
        Log::info("third website------------>");
        // Log::info($crawler->text());
        $newRequest = [];
        $record_exist = FlashNews::where('title', 'like', '%Global tuberculosis report 2021%')->count();
        if ($record_exist == 0) {
            $newRequest['title'] = "Global tuberculosis report 2021";
            $newRequest['href'] = "https://www.who.int/publications/i/item/9789240037021";
            $newRequest['source'] = "https://www.who.int/publications/i/item/9789240037021";
            $newRequest['publish_date'] = $crawler->filter('.dynamic-content__data .dynamic-content__date')->text();
            $newRequest['description'] = $crawler->filter('.dynamic-content__description-container')->text();
            // Log::info($newRequest);

            FlashNews::create($newRequest);
        }

        $crawler = $client->request('GET', 'https://www.who.int/teams/global-tuberculosis-programme/tb-reports');
        Log::info("third website------------>");
        // Log::info($crawler->text());
        $title = $crawler->filter('.sf-publications-wide__header .sf-publications-wide__title')->text();
        $date = $crawler->filter('.sf-publications-wide__body .sf-publications-wide__header .sf-publications-wide__date')->text();
        $href = $crawler->filter('.sf-publications-wide__body .sf-publications-wide__header a')->attr('href');
        $newRequest = [];
        $record_exist = FlashNews::where('title', 'like', '%' . $title . '%')->count();
        if ($record_exist == 0) {

            $newRequest['title'] = $title;
            $newRequest['href'] = "https://www.who.int/" . $href;
            $newRequest['source'] = "https://www.who.int/teams/global-tuberculosis-programme/tb-reports";
            $newRequest['publish_date'] = $date;
            $newRequest['description'] = $crawler->filter('.sf-publications-wide__body .sf-publications-wide__description')->text();

            // Log::info($newRequest);

            FlashNews::create($newRequest);
        }

        $crawler = $client->request('GET', 'https://www.who.int/news-room/fact-sheets/detail/tuberculosis');
        Log::info("fourth website------------>");
        // Log::info($crawler->text());
        $newRequest = [];
        $record_exist = FlashNews::where('title', 'like', 'Tuberculosis')->count();
        if ($record_exist == 0) {
            $newRequest['title'] = "Tuberculosis";
            $newRequest['href'] = "https://www.who.int/news-room/fact-sheets/detail/tuberculosis";
            $newRequest['source'] = "https://www.who.int/news-room/fact-sheets/detail/tuberculosis";
            $newRequest['publish_date'] = $crawler->filter('.date .timestamp')->text();
            // Log::info($crawler->filter('.sf-detail-content .separator-line')->text());
            $newRequest['description'] = $crawler->filter('.sf-detail-content .separator-line')->text();
            // Log::info($newRequest);

            FlashNews::create($newRequest);
        }

        // $data = \Http::get("https://who.tuberculosis.recmap.org/api/recommendations-by-modules");
        // $final_json = json_decode($data->getBody()->getContents(), true);
        // Log::info($final_json);
        // $who_website_content = $final_json['modules'][0]['submodules'][1]['recommendations'];
        // foreach ($who_website_content as $content) {
        //     $record_exist = FlashNews::where('title', 'like', '%' . $content['population'][0]['description'] . '%')->count();
        //     if ($record_exist == 0) {
        //         // Log::info("inside content");
        //         // Log::info($content);
        //         // Log::info($content['recommendation']);
        //         // Log::info($content['population'][0]['description']);
        //         $newRequest['title'] = $content['population'][0]['description'];
        //         $newRequest['description'] = $content['recommendation'];
        //         $newRequest['href'] = "https://who.tuberculosis.recmap.org/recommendation/" . $content['@id'];
        //         $newRequest['source'] = "https://tbksp.org/en/recommendation/page-1";
        //         FlashNews::create($newRequest);
        //     }
        // }

        $data = \Http::get("https://who.tuberculosis.recmap.org/api/recommendations-by-modules");
        $final_json = json_decode($data->getBody()->getContents(), true);
        // Log::info($final_json);
        $who_website_content = $final_json['modules'];
        foreach ($who_website_content as $content) {
            foreach ($content['submodules'] as $data) {
                // Log::info($data);
                foreach ($data['recommendations'] as $recommendation) {
                    // Log::info($recommendation);
                    // Log::info($recommendation['population'][0]['description']);
                    $newRequest['title'] = $recommendation['population'][0]['description'];
                    $newRequest['description'] = $recommendation['recommendation'];
                    $newRequest['href'] = "https://who.tuberculosis.recmap.org/recommendation/" . $recommendation['@id'];
                    $newRequest['source'] = "https://tbksp.org/en/recommendation/page-1";
                    FlashNews::create($newRequest);
                }
            }
            // break;
        }

        $crawler = $client->request('GET', 'https://www.nhp.gov.in/disease/respiratory/lungs/tuberculosis');
        Log::info("sixth website------------>");
        // Log::info($crawler->text());
        $newRequest = [];
        $record_exist = FlashNews::where('title', 'like', 'Disease A-Z Tuberculosis')->count();
        if ($record_exist == 0) {
            $newRequest['title'] = "Disease A-Z Tuberculosis";
            $newRequest['href'] = "https://www.nhp.gov.in/disease/respiratory/lungs/tuberculosis";
            $newRequest['source'] = "https://www.nhp.gov.in/disease/respiratory/lungs/tuberculosis";
            // Log::info($crawler->filter('.sf-detail-content .separator-line')->text());
            // $newRequest['publish_date'] = $crawler->filter('.date .timestamp')->text();
            // Log::info($newRequest);

            FlashNews::create($newRequest);
        }

        // $module_content = \Http::withHeaders([
        //     'Accept' => 'application/json, text/plain, */*',
        //     'Accept-Language' => 'en',
        //     'Authorization' => 'Bearer null',
        //     'Connection' => 'keep-alive',
        //     'Origin' => 'https://swasth-egurukul.in',
        //     'Referer' => 'https://swasth-egurukul.in/',
        //     'Content-Type' => 'application/json',
        //     'Host' => 'apis.swasth-egurukul.in',
        //     'Sec-Fetch-Dest' => 'empty',
        //     'Sec-Fetch-Mode' => 'cors',
        //     'Sec-Fetch-Site' => 'same-site',
        //     'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36',
        //     'sec-ch-ua-mobile' => '?0',
        //     'sec-ch-ua-platform' => "Linux",
        // ])->post(
        //     'https://apis.swasth-egurukul.in/api/v1/module/list',
        //     [
        //         'projectId' => 11030
        //     ]
        // )->collect()->toArray();

        // // Log::info($module_content);

        // foreach ($module_content['data'] as $module) {
        //     $record_exist = FlashNews::where('title', 'like', '%' . $module['title'] . '%')->count();
        //     if ($record_exist == 0) {
        //         $newRequest['title'] = $module['title'];
        //         $newRequest['description'] = $module['discription'];
        //         $newRequest['source'] = "https://swasth-egurukul.in/modules/11030";
        //         $newRequest['href'] = 'https://swasth-egurukul.in/modules/11030';
        //         FlashNews::create($newRequest);
        //     }
        // }
        $this->info("Flash News Website Content done!!!");
        return  0;
    }
}
