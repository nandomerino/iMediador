<?php

namespace App\Http\Controllers;

use DebugBar\DebugBar;
use Illuminate\Http\Request;


class SliderController extends Controller
{

    private $data;

    public function do(Request $request)
    {

        // Gets sent variables variables
        $this->data = $request->all();

        switch ($this->data["action"]) {
            case "save":
                $response = $this->save();
                break;
            case "getAll":
                $response = $this->getAll();
                break;
            case "get":
                $response = $this->get();
                break;
            case "delete":
                $response = $this->delete();
                break;
            default:
                $response = false;
        }
        return $response;
    }

    public function save()
    {

        // STORES VARIABLES
        if( $this->data['id'] && $this->data['id'] > 0 ){
            // uses provided id to overwrite changes
            $id = $this->data['id'];
        }else{
            // gets new id for new content
            $filelist = scandir(config('filesystems.disks.local.sliders') );
            if( count($filelist) > 3) {
                $idList = [];
                $i = 0;
                foreach ($filelist as $row) {
                    if ($row != "." &&
                        $row != ".." &&
                        $row != "index.html" &&
                        !strpos($row, ".jpg")) {
                        $idList[$i] = $row;
                        $i++;
                    }
                }
                $return = $idList;

                sort($idList, SORT_NUMERIC);
                $id = $idList[count($idList) - 1] + 1;
            }else{
                $id = 1;
            }
        }

        $file = "<?php\n";
        $file .= "\$name = '" . $this->data['name'] . "';\n";
        $file .= "\$color = '" . $this->data['color'] . "';\n";
        $file .= "\$header = '" . $this->data['header'] . "';\n";
        $file .= "\$description = '" . $this->data['description'] . "';\n";
        $file .= "\$fInicio = '" . $this->data['fInicio'] . "';\n";
        $file .= "\$fFinal = '" . $this->data['fFinal'] . "';\n";
        $file .= "?>";

        file_put_contents(config('filesystems.disks.local.sliders') . $id , $file);

        // STORES IMAGE

        $data = explode( ',', $this->data['image'] );
        file_put_contents(config('filesystems.disks.local.sliders') . $id . ".jpg" , base64_decode( $data[1] ) );

        return "OK";
    }

    public function delete()
    {
        unlink( config('filesystems.disks.local.sliders') . $this->data['id'] . ".jpg" );
        unlink( config('filesystems.disks.local.sliders') . $this->data['id'] );

        return "OK";
    }


    public function getAll()
    {
        $filelist = scandir(config('filesystems.disks.local.sliders') );
        $sliderList = [];
        $i = 0;

        // Creates processed array
        foreach($filelist as $row){
            if( $row != "." &&
                $row != ".." &&
                $row != "index.html" &&
                !strpos($row, ".jpg") ){

                $idList[$i] = $row;
                $i++;
            }
        }


        // Generates output code depending on provided type
        switch( $this->data['output']){
            case "table":
                rsort($idList, SORT_NUMERIC);

                // Generates HTML for table
                $output = "";
                foreach($idList as $row) {

                    // Load data from file
                    require( config('filesystems.disks.local.sliders') . $row);
                    $modified = date( "d/m/Y", filemtime ( config('filesystems.disks.local.sliders') . $row ) );

                    $output .= "<tr>";
                    $output .= "<td>" . $row . "</td>" ;
                    $output .= "<td>" . $name . "</td>" ;
                    $output .= "<td>" . $fInicio . "</td>" ;
                    $output .= "<td>" . $fFinal . "</td>" ;
                    $output .= "<td>" . $modified . "</td>" ;
                    $output .= '<td>
                                    <img class="edit-button action-button" data-id="' . $row . '" alt="' . __('panel.sliders.table.actions.edit') . '" title="' . __('panel.sliders.table.actions.edit') . '" src="/img/edit.png">
                                    <img class="delete-button action-button" data-id="' . $row . '" alt="' .  __('panel.sliders.table.actions.delete') . '" title="' . __('panel.sliders.table.actions.delete') . '" src="/img/trashcan.png">
                                </td>' ;

                    $output .= "</tr>";
                }
                break;

            case "slider":
                // Generates HTML for slider

                app('debugbar')->info(session('home.showSliders') );

                $output = "";
                if( is_array(session('home.showSliders') )){
                    foreach( session('home.showSliders') as $find){
                        foreach($idList as $row) {
                            if( $find == $row){
                                // Load data from file
                                require( config('filesystems.disks.local.sliders') . $row);

                                $output .= '<li class="splide__slide" style="background-image: url(\'/sliders/' . $row . '.jpg\')">';
                                $output .= '<h2 style="color: ' . $color . ';">' . $header . '</h2>';
                                $output .= '<h4 style="color: ' . $color . ';">' . $description . '</h4>';
                                $output .= '</li>';
                                break;
                            }
                        }
                    }

                } else {
                    $row = session('home.showSliders');


                    $output .= '<li class="splide__slide" style="background-image: url(\'/sliders/' . $row . '.jpg\')">';
                    //$output .= '<h2 style="color: ' . $color . ';">' . $header . '</h2>';
                    //$output .= '<h4 style="color: ' . $color . ';">' . $description . '</h4>';
                    $output .= '</li>';

                }


                break;
        }


        return $output;
    }

    public function get()
    {
        // LOAD VARIABLES
        require( config('filesystems.disks.local.sliders') . $this->data['id']);
        $image = file_get_contents( config('filesystems.disks.local.sliders') . $this->data['id'] . ".jpg" );

        $sliderInfo['id'] = $this->data['id'];
        $sliderInfo['name'] = $name;
        $sliderInfo['color'] = $color;
        $sliderInfo['header'] = $header;
        $sliderInfo['description'] = $description;
        if (isset($sliderInfo['fInicio'])) {
            $sliderInfo['fInicio'] = $fInicio;
        }
        if (isset($sliderInfo['fFinal'])) {
            $sliderInfo['fFinal'] = $fFinal;
        }
        $sliderInfo['image'] = "data:image/jpg;base64," . base64_encode($image);

        return $sliderInfo;
    }
}
