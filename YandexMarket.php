<?
class Nws_Yml_YandexMarket {
    protected
        $name,
        $url,
        $date,
        $company,
        $delivery,
        $deliveryPrice,
        $currency,
        $currencyRate,
        $category;
    
    protected
        $xml,
        $offers,
        $shop,
        $yml_catalog;
    
    public function configGenerate($name,$url,$company,$delivery,$deliveryPrice){
        $this->name = $name;
        $this->url = $url;
        $this->company = $company;
        $this->delivery = $delivery; 
        $this->deliveryPrice = $deliveryPrice;
        $this->date = date('Y-m-d H:i');
        $this->currency = "RUB";
        $this->currencyRate = 1;
        $this->category = 'all';
    }
    public function createYML(){
        $this->xml = new DOMDocument('1.0', 'utf-8');
        $this->yml_catalog = $this->xml->createElement( 'yml_catalog' );
        $this->xml->appendChild($this->yml_catalog);
        $this->yml_catalog->setAttribute( "date", $this->date );
        $this->shop = $this->xml->createElement( 'shop' );
        $this->yml_catalog->appendChild($this->shop);
        $this->shop->appendChild($this->xml->createElement('name',$this->name));
        $this->shop->appendChild($this->xml->createElement('company',$this->company));
        $this->shop->appendChild($this->xml->createElement('url',$this->url));
        $currency = $this->xml->createElement('currency');
        $currency->setAttribute( "id", $this->currency);
        $currency->setAttribute( "rate", $this->currencyRate);
        $currencies = $this->xml->createElement( 'currencies' );
        $currencies->appendChild($currency);
        $this->shop->appendChild($currencies);
        $category = $this->xml->createElement('category',$this->category);
        $category->setAttribute( "id", '1');
        $categories = $this->xml->createElement( 'categories' );
        $categories->appendChild($category);
        $this->shop->appendChild($categories);
        $this->offers = $this->xml->createElement( 'offers' );
        $this->shop->appendChild($this->offers);
    }
    public function saveYML($path){
        $this->xml->preserveWhiteSpace = false;
        $this->xml->formatOutput = true;
        $this->xml->save($path);
    }
    /*
    $params = array(
        'url' => 'url',
        'price' => 'price',
        'currencyId' => 'currencyId',
        'categoryId' => 'categoryId',
        'picture' => 'picture',
        'delivery' => 'delivery',
        'local_delivery_cost' => 'local_delivery_cost',
        'name' => 'name',
        'description' => 'description',
    )
    */
    public function addOffer($params,$id){
        $offer = $this->xml->createElement( 'offer' );
        $offer->setAttribute( "id", $id);
        $offer->setAttribute( "available", "true");
        foreach($params as $key=>$param){
            $offer->appendChild($this->xml->createElement($key,$param));  
        }
        $offer->appendChild($this->xml->createElement('currencyId',$this->currency)); 
        $offer->appendChild($this->xml->createElement('categoryId','1')); 
        $this->offers->appendChild($offer);
    }
}
?>