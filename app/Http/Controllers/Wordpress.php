<?php

namespace App\Http\Controllers;

class Wordpress extends Controller
{
    /**
     * Retrieves Data from Wordpress REST API
     * @param $type
     * @param $value
     * @return mixed
     */

    public function get($type, $method, $value)
    {
        switch($type){
            case "pages":
                switch($method) {
                    case "slug":
                        $query = config('filesystems.disks.public.WPapi') . $type . "?" . $method . "=" . $value . "&_embed";
                        break;
                }
                break;

            case "posts":
                switch($method) {
                    case "slug":
                        $query = config('filesystems.disks.public.WPapi') . $type . "?" . $method . "=" . $value . "&_embed";
                        break;
                }
                break;
        }

        $data = array();
        if( isset($query) ){
            // Gets data as JSON


            $json = file_get_contents($query);
            if( $json != "[]" ){
                // Decodes JSON
                $obj = json_decode($json);

                // Loads content depending on type
                switch($type) {
                    case "pages":
                        $data['content'] = "<div class='wp-data page $value'>" . $obj[0]->content->rendered . "</div>";
                        $data['title'] = $obj[0]->title->rendered;
                        break;

                    case "posts":
                        $data['content'] = "<div class='wp-data post $value'>";
                        $data['content'] .= "<div class='featured-image'><img src='". $obj[0]->fimg_url . "'></div>";
                        $data['content'] .= "<div class='title'><h1>" . $obj[0]->title->rendered . "</h1></div>";
                        $data['content'] .= "<div class='content'>" . $obj[0]->content->rendered . "</div>";
                        $data['content'] .= "</div>";
                        $data['title'] = $obj[0]->title->rendered;
                        break;
                }
            }else{
                $data = __('wordpress.json.empty');
            }
        }else{
            $data = __('wordpress.json.parameters.wrong');
        }

        return $data;
    }
}
