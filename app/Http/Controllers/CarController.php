<?php
namespace App\Http\Controllers;

use App\Models\Car;
use Curl;
use Htmldom;

class CarController extends Controller
{
    public function index(){
        return view('car.list', ['cars' =>Car::all()]);
    }

    /**
     * Descarga el listado de carros desde una pagina externa y los inserta a la tabla "cars"
     */
    public function download(){
        $carsArray = [];

        $page = 1;
        do{
            $carsListArray = $this->getCarsList($page);
            $carsArray = array_merge($carsArray, $carsListArray);
            $page++;
        }while(count($carsArray) < 50 && count($carsListArray) > 0);

        //Inserta los registros a la BD
        foreach ($carsArray as $carArray){
            Car::create($carArray);
        }

        return redirect()->route('cars.index');
    }

    public function deleteAll(){
        Car::getQuery()->delete();

        return redirect()->route('cars.index');
    }

    protected function getCarsList($page = 1){
        $curlResponse = Curl::to("http://www.avisosdeocasion.com/vehiculos-usados-y-nuevos.aspx")
            ->withData([
                'n' => 'autos-chevrolet-usados-y-nuevos-nuevo-leon',
                'PlazaBusqueda' => 2, 'Plaza' => 2, 'pagina' => $page,
                'idvehiculo' => 1, 'Marcas' => 11
            ])->get();

        $cars = [];

        $html = new Htmldom($curlResponse);
        $pricesTitles = $html->find('td.tituloresult');
        foreach ($pricesTitles as $index => $title){
            //Paseando el Precio
            $price = $title->children(0)->children(0)->text();
            $cars[$index]['price'] = $this->numberFormat($this->formatText($price));

            //Parseando el Nombre
            $price = $title->children(1)->children(0)->children(0)->text();
            $cars[$index]['name'] = $this->formatText($price);

            $attrBody = $title->parent()->parent()->next_sibling();

            if ($attrBody != null){
                $cars[$index]['year'] = $this->numberFormat($attrBody->children(0)->children(0)->children(0)->text());
                $cars[$index]['transmission'] = $this->getTransmissionType($attrBody->children(0)->children(1)->children(0)->text());
                $cars[$index]['doors'] = $this->numberFormat($attrBody->children(2)->children(0)->children(0)->text());
                $cars[$index]['km'] = $this->numberFormat($attrBody->children(3)->children(0)->text()) * 1000;
            }else{
                $attrBody = $title->parent()->parent()->parent()->parent()->next_sibling()->children(0)->children(0)->children(0)->children(0);
                $cars[$index]['year'] = $this->numberFormat($attrBody->children(0)->text());
                $cars[$index]['transmission'] = $this->getTransmissionType($attrBody->children(3)->text());
                $cars[$index]['doors'] = $this->numberFormat($attrBody->children(2)->text());
                $cars[$index]['km'] = $this->numberFormat($attrBody->children(3)->text()) * 1000;
            }

            //Parseo de la url de la foto del carro
            $imageBody = $title->parent()->parent()->parent()->prev_sibling();
            if ($imageBody){
                $cars[$index]['image_url'] = $this->resizeImageUrl($imageBody->find('.imgfotoaviso')[0]->src);
            }else{
                $imageBody = $title->parent()->parent()->parent()->parent()->parent()->parent()->prev_sibling();
                $cars[$index]['image_url'] = $this->resizeImageUrl($imageBody->find('.imgfotoaviso')[0]->src);
            }
        }

        return $cars;
    }

    /**
     * Remueve tags de html y espacios vacíos
     *
     * @param $text
     * @return string
     */
    private function formatText($text){
        return trim(strip_tags($text, '<a>'));
    }

    private function numberFormat($text){
        return preg_replace('/\D/', '', $this->formatText($text));
    }

    private function resizeImageUrl($imageUrl){
        return str_replace(['width=140', 'height=100'], ['width=500', 'height=380'], $imageUrl);
    }

    private function getTransmissionType($text){
        if (strpos($this->formatText($text),'aut') !== false) {
            return 'Automático';
        }elseif (strpos($this->formatText($text),'est') !== false) {
            return 'Estándar';
        }

        return '';
    }
}