<?php

class Tscraper
{
    public static function ScrapeId($id)
    {
        $torrent = new Torrent(TORRENTDIR . "/$id.torrent");
        try {
            $scraped = $torrent->scrape();
        } catch (\Exception $e) {
            //$scraped = $torrent->errors();
            //exit();
        }
        $myarray = array_shift($scraped);

        $seeders = $leechers = $completed = 0;
        if ($myarray['seeders'] > 0) {
            $seeders = $myarray['seeders'];
        }
        if ($myarray['leechers'] > 0) {
            $leechers = $myarray['leechers'];
        }
        if ($myarray['completed'] > 0) {
            $completed = $myarray['completed'];
        }

        if ($seeders !== null) {
            // Update the Torrent
            DB::run(" UPDATE torrents
                    SET leechers = ?, seeders = ?, times_completed = ?, last_action = ?, visible = ?
                    WHERE id = ?",
                    [$leechers, $seeders, $completed, TimeDate::get_date_time(), 'yes', $id]
            );
        } else {
            // Its Dead :(
            DB::run("UPDATE torrents SET last_action = ? WHERE id=?", [TimeDate::get_date_time(), $id]);
        }
    }

    public static function scrapeall()
    {
        // Set A Limit ? how fast is server / how many torrents to limit ?
        //set_time_limit(15);

        // Rescrape torrents every x seconds. (Default: 2 days)
        $stmt = DB::run("SELECT `id`, `info_hash`, `last_action` 
                         FROM `torrents` 
                         WHERE `external` = 'yes' 
                         AND `last_action` <= DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 DAY)");

        foreach ($stmt as $tor) {
            self::ScrapeId($tor['id']);
        }
    }
}