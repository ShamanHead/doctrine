<?php
set_time_limit(-1);

require_once('Parser.php');

use Parser\Dom as Dom;

class HTMLtoCSV{
  private $cookiesEng = [[CURLOPT_COOKIE, 'PHPSESSID=8c3d65cb900c0b1aab122c3562f0949d; wooTracker=7jMWx752g7oO; _ym_uid=160348684932911205; _ym_d=1603486849; _ga=GA1.2.1876053663.1603486849; _gid=GA1.2.2129659833.1603486849; _fbp=fb.1.1603486849363.1913789123; _dc_gtm_UA-2462638-12=1; _ym_isad=2; _ym_visorc_46447131=w; clientCurrency=usd; clientLanguage=ENG']];
  private $cookiesRu = [[CURLOPT_COOKIE, 'PHPSESSID=8c3d65cb900c0b1aab122c3562f0949d; wooTracker=7jMWx752g7oO; _ym_uid=160348684932911205; _ym_d=1603486849; _ga=GA1.2.1876053663.1603486849; _gid=GA1.2.2129659833.1603486849; _fbp=fb.1.1603486849363.1913789123; _dc_gtm_UA-2462638-12=1; _ym_isad=2; _ym_visorc_46447131=w; clientCurrency=usd; clientLanguage=RUS']];
  private $path = '/home/j/jarmo/watches-pawnshop.com/public_html/images/virtuemart/product/';
  private $csv_path = '/home/j/jarmo/watches-pawnshop.com/public_html/csvcat/';
  private $ids = [];
  private $ru_ids = [];

  function __construct(){
    $this->frezer_eng_csv = $this->parseFirst();
    $this->frezer_eng_csv_juw = $this->parseThird();

    $this->buildFirst($this->frezer_eng_csv);
    $this->buildThird($this->frezer_eng_csv_juw);

    $this->frezer_ru_csv = $this->parseFirst(true);
    $this->frezer_ru_csv_juw = $this->parseThird(true);

    $this->buildFirst($this->frezer_ru_csv, true);
    $this->buildThird($this->frezer_ru_csv_juw, true);

    $this->lombard_eng_csv = $this->parseSecond();
    $this->lombard_eng_csv_juw = $this->parseFourth();

    $this->buildSecond($this->lombard_eng_csv);
    $this->buildFourth($this->lombard_eng_csv_juw);

    $this->lombard_ru_csv = $this->parseSecond(true);
    $this->lombard_ru_csv_juw = $this->parseFourth(true);

    $this->buildSecond($this->lombard_ru_csv, true);
    $this->buildFourth($this->lombard_ru_csv_juw, true);
  }

  private function parseFirst($ru = false) : array {
    $href = '.com';
    if($ru) $href = '.ru';
    $some = new Dom('http://frezerhouse'.$href.'/products/shveytsarskie-chasy/');
    $item = $some->find('.modern-page-navigation')->children(6);

    $MAX_POG = $some->find('.modern-page-navigation')->children(6)->plainText()->merge();

    $result = [];

    for($i = 1;$i <= $MAX_POG;$i++){
      if($ru == '.ru'){
        $find = new Dom('http://frezerhouse.ru/products/shveytsarskie-chasy/?PAGEN_2='.$i);
      }else{
        $find = new Dom('http://frezerhouse.com/en/products/shveytsarskie-chasy/?PAGEN_2='.$i);
      }
      $hrefs = $find->find('.product-block__title')->findProperty('href');
      $result[] = $hrefs;
      unset($find);
    }

    $t_result = [];

    for($i = 0;$i < count($result);$i++){
      for($j = 0;$j < count($result[$i]);$j++){
          $t_result[] = $result[$i][$j];
      }
    }

    $result = $t_result;

    print_r($result);
    $con = [];
    for($i = 0;$i < count($result);$i++){
      $params = [];
      $test = new Dom('http://frezerhouse'.$href.$result[$i]);
      $seen_params = $test->find('.product__details-label');
      $seen_values = $test->find('.product__details-value');
      $name = $test->find('.product__title', 0)->plainText()->merge();
      if(!$ru){
        $price = $test->find('.buy-info_price', 0)->plainText()->merge();
        if($price == ''){
          $price = $test->find('.buy-info_price', 0)->children(0)->plainText()->merge();
        }
        $pattern = '/[$a-zA-Z ]/';
        $params['Price'] = preg_replace($pattern, '', $price);
      }else{
        if($test->find('.product__info', 0)->__COUNT-1 > 2) $params['desc'] = $test->find('.text-format-limit')->children(0)->plainText()->merge();
        $params['Price'] = $this->frezer_eng_csv[$i]['Price'];
      }
      $params['Name'] = $name;

      for($j = 0;$j <= $seen_params->__COUNT-1;$j++){
        if(!$seen_params->children($j)->children(0)->__NOT_FOUND){
          $params[$seen_params->children($j)->children(0)->plainText()->merge()] = $seen_values->children($j)->children(0)->plainText()->merge();
        }
      }
      if(!$test->find('.product__info')->children(1)->__NOT_FOUND){
        $hidden = $test->find('.product__details-hidden');
        for($j = 0;$j < $hidden->__COUNT;$j++){
          if(!$hidden->children($j)->children(0)->children(0)->__NOT_FOUND){
              $params[$hidden->children($j)->children(0)->children(0)->plainText()->merge()] = $hidden->children($j)->children(1)->plainText()->merge();
          }
        }
      }
      // print_r($params);
      if(!$ru){
        if(empty($params['Ref. No./Code'])){
          $params['id'] = explode('/',$result[$i])[4];
        }else{
          $params['id'] = $params['Ref. No./Code'];
          unset($params['Ref. No./Code']);
        }
      }else{
        if(empty($params['Номер модели/Ref.'])){
          $params['id'] = explode('/',$result[$i])[3];
        }else{
          $params['id'] = $params['Номер модели/Ref.'];
          unset($params['Номер модели/Ref.']);
        }
      }
      $founded = false;
      if(!$ru){
        for($j = 0;$j < count($this->ids);$j++){
          if(strcasecmp($this->ids[$j],$params['id']) == 0){
            $founded = true;
          }
        }
          if(!$founded) $this->ids[] = $params['id'];
      }else{
        for($j = 0;$j < count($this->ru_ids);$j++){
          if(strcasecmp($this->ru_ids[$j],$params['id']) == 0){
            $founded = true;
          }
        }
        if(!$founded) $this->ru_ids[] = $params['id'];
      }
      if($founded) continue;
      $img_name = '';
      if(!$ru){
        $img = $test->find('#imgarea-big')->findProperty('src');
        for($j = 0;$j < count($img);$j++){
          $format = mb_strtolower(explode('.', $img[$j])[count(explode('.', $img[$j]))-1]);
          if($format == 'jpeg') $format = 'jpg';
          if($j == count($img)-1){
            $img_name .= explode('/',$result[$i])[4].$j.'.'.$format;
          }else{
              $img_name .= explode('/',$result[$i])[4].$j.'.'.$format.'|';
          }
          if(!file_exists($this->path.explode('/',$result[$i])[4].$j.'.'.$format)) copy('http://frezerhouse'.$href.$img[$j], $this->path.explode('/',$result[$i])[4].$j.'.'.$format);
        }
        $params['img'] = $img_name;
      }else{
        $found = false;
        for($z = 0;$z < count($this->frezer_eng_csv);$z++){
          if($this->frezer_eng_csv[$z]['id'] == $params[$z]['id']){
            $params['img'] = $this->frezer_eng_csv[$z]['img'];
            $found = true;
          }
        }
        if(!$found) continue;
      }
      $con[] = $params;
      print_r($params['Price']);
      echo "$i ";
    }

    // print_r($con);
    return $con;
  }

  private function parseSecond($ru = false) : array{

    $con = [];

    $result = [];

    $find = new Dom('https://www.lombard-watch.ru/katalog/watch/');
    $hrefs = $find->find('.catalogItem')->findProperty('href');
    print_r($hrefs);
    unset($find);

    for($i = 0;$i < count($hrefs);$i++){
      $temp = new Dom('https://www.lombard-watch.ru/'.$hrefs[$i], false, false, $ru ? $this->cookiesRu : $this->cookiesEng);
      $params = [];
      $name = $temp->find('.col-md-7')->find('h1')->children(0)->plainText()->merge();
      $format = mb_strtolower(explode('.',$img)[2]);
      $price = $temp->find('.price')->children(0)->plainText()->merge();
      $itemName = $temp->find('.name');
      $itemValue = $temp->find('.value');
      $itemTdValue = $temp->find('td');
      if($temp->find('.itemTable')->children(0)->children(1)->__NOT_FOUND == false){
        $desc = $temp->find('.itemTable')->children(0)->children(1)->plainText()->merge();
      }else{
        $desc = '';
      }
      $pattern = '/[$ ]/';
      $price = preg_replace($pattern, '', $price);
      $params = ['Name' => $name, 'Price' => $price, 'desc' => $desc];

      for($j = 0;$j < $itemName->__COUNT;$j++){
        if(!$itemName->children($j)->__NOT_FOUND){
            $params[$itemName->children($j)->plainText()->merge()] = $itemValue->children($j)->plainText()->merge();
        }
      }
      for($j = 0;$j < $itemTdValue->__COUNT;$j++){
        if(!$itemTdValue->children($j)->__NOT_FOUND){
            $params[$itemTdValue->children($j)->children(0)->plainText()->merge()] = $itemTdValue->children($j)->children(1)->plainText()->merge();
        }
      }
      if(!$ru){
        if(empty($params['Reference'])){
          $params['id'] = explode('/', $hrefs[$i])[3];
        }else{
          $params['id'] = $params['Reference'];
          unset($params['Reference']);
        }
      }else{
        if(empty($params['Референс'])){
          $params['id'] = explode('/', $hrefs[$i])[3];
        }else{
          $params['id'] = $params['Референс'];
          unset($params['Референс']);
        }
      }
      $founded = false;
      if(!$ru){
        for($j = 0;$j < count($this->ids);$j++){
          if(strcasecmp($this->ids[$j],$params['id']) == 0){
            $founded = true;
          }
        }
        if(!$founded) $this->ids[] = $params['id'];
      }else{
        for($j = 0;$j < count($this->ru_ids);$j++){
          if(strcasecmp($this->ru_ids[$j],$params['id']) == 0){
            $founded = true;
          }
        }
        if(!$founded) $this->ru_ids[] = $params['id'];
      }
      if($founded) continue;
      if(!$ru){
        $img_name = '';
        $imgs = [];
        for($j = 0;$j < $temp->find('.col-md-5')->__COUNT-1;$j++){
            $imgs[] = $temp->find('.col-md-5')->children($j)->children(0)->viewDom()['src'];
        }
        for($j = 0;$j < count($imgs);$j++){
          $format = mb_strtolower(explode('.', $imgs[$j])[count(explode('.', $imgs[$j]))-1]);
            if($format == 'jpeg') $format = 'jpg';
            if($j == count($imgs)-1){
              $img_name .= explode('/', $hrefs[$i])[count(explode('/', $hrefs[$i]))-1].$j.'.'.$format;
            }else{
                $img_name .= explode('/', $hrefs[$i])[count(explode('/', $hrefs[$i]))-1].$j.'.'.$format.'|';
            }
            if(!file_exists($this->path.explode('/', $hrefs[$i])[count(explode('/', $hrefs[$i]))-1].$j.'.'.$format)) copy('https://www.lombard-watch.ru/'.$imgs[$j], $this->path.explode('/', $hrefs[$i])[count(explode('/', $hrefs[$i]))-1].$j.'.'.$format);
          }
          $params['img'] = $img_name;
      }else{
        $params['img'] = $this->lombard_eng_csv[$i]['img'];
      }
      $con[] = $params;
      echo "$i ".count($hrefs);
    }
    return $con;
  }

  public function parseThird($ru = false){
    $href = '.com';
    if($ru) $href = '.ru';
    $some = new Dom('http://frezerhouse'.$href.'/products/yuvelirnye-izdeliya/');

    if(!$some->find('.frame__main')->children(1)->__NOT_FOUND){
      $MAX_POG = $some->find('.modern-page-navigation')->children(6)->plainText()->merge();
    }

    $result = [];
    if(isset($MAX_POG)){
      for($i = 1;$i <= $MAX_POG;$i++){
        if($ru){
          $find = new Dom('http://frezerhouse.ru/products/yuvelirnye-izdeliya/?PAGEN_1='.$i);
        }else{
          $find = new Dom('http://frezerhouse.com/en/products/yuvelirnye-izdeliya/?PAGEN_1='.$i);
        }
        $hrefs = $find->find('.product-block__title')->findProperty('href');
        $result[] = $hrefs;
        unset($find);
      }
    }else{
      if(!$ru){
        $find = new Dom('http://frezerhouse.com/en/products/yuvelirnye-izdeliya/');
        $result = $find->find('.product-block__title')->findProperty('href');
      }else{
        $find = new Dom('http://frezerhouse.ru/products/yuvelirnye-izdeliya/');
        $result = $find->find('.product-block__title')->findProperty('href');
      }
    }

    if($MAX_POG){
      $t_result = [];

      for($i = 0;$i < count($result);$i++){
        for($j = 0;$j < count($result[$i]);$j++){
            $t_result[] = $result[$i][$j];
        }
      }
      $result = $t_result;
    }
    $con = [];
    for($i = 0;$i < count($result);$i++){
      $params = [];
      $test = new Dom('http://frezerhouse'.$href.$result[$i]);
      $seen_params = $test->find('.product__details-label');
      $seen_values = $test->find('.product__details-value');
      $name = $test->find('.product__title', 0)->plainText()->merge();
      if(!$ru){
        $price = $test->find('.buy-info_price',0)->plainText()->merge();
        if($price == ''){
          $price = $test->find('.buy-info_price', 0)->children(0)->plainText()->merge();
        }
        $pattern = '/[$a-zA-Z ]/';
        $params['Price'] = preg_replace($pattern, '', $price);
      }else{
        if($test->find('.product__info', 0)->__COUNT-1 > 2) $params['desc'] = $test->find('.text-format-limit')->children(0)->plainText()->merge();
        $params['Price'] = $this->frezer_eng_csv_juw[$i]['Price'];
      }
      $params['Name'] = $name;

      for($j = 0;$j <= $seen_params->__COUNT;$j++){
        if(!$seen_params->children($j)->children(0)->__NOT_FOUND){
          $params[$seen_params->children($j)->children(0)->plainText()->merge()] = $seen_values->children($j)->children(0)->plainText()->merge();
        }
      }
      if(!$ru){
        if(empty($params['Ref. No./Code'])){
          $params['id'] = explode('/',$result[$i])[4];
        }else{
          $params['id'] = $params['Ref. No./Code'];
          unset($params['Ref. No./Code']);
        }
      }else{
        if(empty($params['Номер модели/Ref.'])){
          $params['id'] = explode('/',$result[$i])[3];
        }else{
          $params['id'] = $params['Номер модели/Ref.'];
          unset($params['Номер модели/Ref.']);
        }
      }
      $founded = false;
      if(!$ru){
        for($j = 0;$j < count($this->ids);$j++){
          if(strcasecmp($this->ids[$j],$params['id']) == 0){
            $founded = true;
          }
        }
        if(!$founded) $this->ids[] = $params['id'];
      }else{
        for($j = 0;$j < count($this->ru_ids);$j++){
          if(strcasecmp($this->ru_ids[$j],$params['id']) == 0){
            $founded = true;
          }
        }
        if(!$founded) $this->ru_ids[] = $params['id'];
      }
      if($founded) continue;
      $img_name = '';
      if(!$ru){
        $img = $test->find('#imgarea-big')->findProperty('src');
        for($j = 0;$j < count($img);$j++){
          $format = mb_strtolower(explode('.', $img[$j])[count(explode('.', $img[$j]))-1]);
          if($format == 'jpeg') $format = 'jpg';
          if($j == count($img)-1){
            $img_name .= explode('/',$result[$i])[4].$j.'.'.$format;
          }else{
              $img_name .= explode('/',$result[$i])[4].$j.'.'.$format.'|';
          }
          if(!file_exists($this->path.explode('/',$result[$i])[4].$j.'.'.$format)) copy('http://frezerhouse'.$href.$img[$j], $this->path.explode('/',$result[$i])[4].$j.'.'.$format);
        }
        $params['img'] = $img_name;
      }else{
        $found = false;
        for($z = 0;$z < count($this->frezer_eng_csv_juw);$z++){
          if($this->frezer_eng_csv_juw[$z]['id'] == $params[$z]['id']){
            $params['img'] = $this->frezer_eng_csv_juw[$z]['img'];
            $found = true;
          }
        }
        if(!$found) continue;
      }
      $con[] = $params;
      echo "$i ".count($result);
    }

    return $con;
  }

  public function parseFourth($ru = false){

    $con = [];

    $result = [];

    $find = new Dom('https://www.lombard-watch.ru/katalog/jewelery');
    $hrefs = $find->find('.catalogItem')->findProperty('href');
    print_r($hrefs);
    unset($find);

    for($i = 0;$i < count($hrefs);$i++){
      $temp = new Dom('https://www.lombard-watch.ru/'.$hrefs[$i], false, false, $ru ? $this->cookiesRu : $this->cookiesEng);
      $params = [];
      $name = $temp->find('.col-md-7')->find('h1')->children(0)->plainText()->merge();
      $price = $temp->find('.price')->children(0)->plainText()->merge();
      $itemName = $temp->find('.name');
      $itemValue = $temp->find('.value');
      $type = $temp->find('.breadcrumb-item',4)->children(0)->plainText()->merge();
      if(!$ru){
        switch($type){
          case 'Кольца':
            $type = 'rings';
          break;
          case 'Браслеты':
            $type = 'bracelets';
          break;
          case 'Серьги':
            $type = 'earrings';
          break;
          case 'Подвески':
            $type = 'pendants';
          break;
          case 'Комплекты':
            $type = 'kits';
          break;
          case 'Колье':
            $type = 'necklace';
          break;
          case 'Запонки':
            $type = 'cufflinks';
          break;
          case 'Заготовки':
            $type = 'blanks';
          break;
          case 'Цепочки':
            $type = 'chains';
          break;
          case 'Другое':
            $type = 'other';
          break;
        }
      }
      $pattern = '/[$ ]/';
      $price = preg_replace($pattern, '', $price);
      $params = ['Name' => $name, 'Price' => $price, 'desc' => '', 'type' => $type];

      if(!$ru){
        $img_name = '';
        $imgs = [];
        for($j = 0;$j < $temp->find('.col-md-5')->__COUNT-1;$j++){
          $src = $temp->find('.col-md-5')->children($j)->children(0)->viewDom()['src'];
          if($src){
              $imgs[] = $src;
          }
        }
        for($j = 0;$j < count($imgs);$j++){
          $format = mb_strtolower(explode('.', $imgs[$j])[count(explode('.', $imgs[$j]))-1]);
            if($format == 'jpeg') $format = 'jpg';
            if($j == count($imgs)-1){
              $img_name .= explode('/', $hrefs[$i])[count(explode('/', $hrefs[$i]))-1].$j.'.'.$format;
            }else{
                $img_name .= explode('/', $hrefs[$i])[count(explode('/', $hrefs[$i]))-1].$j.'.'.$format.'|';
            }
            if(!file_exists($this->path.explode('/', $hrefs[$i])[count(explode('/', $hrefs[$i]))-1].$j.'.'.$format)) copy($this->path.$imgs[$j], 'imgs/'.explode('/', $hrefs[$i])[count(explode('/', $hrefs[$i]))-1].$j.'.'.$format);
          }
          $params['img'] = $img_name;
      }else{
        $params['img'] = $this->lombard_eng_csv_juw[$i]['img'];
      }
      if(!$ru){
        if(empty($params['Reference'])){
          $params['id'] = explode('/', $hrefs[$i])[3];
        }else{
          $params['id'] = $params['Reference'];
          unset($params['Reference']);
        }
      }else{
        if(empty($params['Референс'])){
          $params['id'] = explode('/', $hrefs[$i])[3];
        }else{
          $params['id'] = $params['Референс'];
          unset($params['Референс']);
        }
      }
      $founded = false;
      if(!$ru){
        for($j = 0;$j < count($this->ids);$j++){
          if(strcasecmp($this->ids[$j],$params['id']) == 0){
            $founded = true;
          }
        }
        if(!$founded) $this->ids[] = $params['id'];
      }else{
        for($j = 0;$j < count($this->ru_ids);$j++){
          if(strcasecmp($this->ru_ids[$j],$params['id']) == 0){
            $founded = true;
          }
        }
        if(!$founded) $this->ru_ids[] = $params['id'];
      }
      if($founded) continue;
      for($j = 0;$j < $itemName->__COUNT;$j++){
        if(!$itemName->children($j)->__NOT_FOUND){
            if($itemName->children($j)->plainText()->merge() == 'More info' || $itemName->children($j)->plainText()->merge() == 'Подробнее'){
              $text = '';
              $x = 0;
              while(!$itemValue->children($j)->children($x)->__NOT_FOUND){
                $text .= $itemValue->children($j)->children($x)->plainText()->merge();
                $x++;
              }
              $params['desc'] = $text;
            }else{
              $params[$itemName->children($j)->plainText()->merge()] = $itemValue->children($j)->plainText()->merge();
            }
        }
      }
      echo "$i ".count($hrefs);
      $con[] = $params;
    }
    return $con;
  }
  public function buildFirst($json, $ru = false){
    if(!$ru){
      $fp = fopen($this->csv_path.'eng.csv', 'w+');
      $fp1 = fopen($this->csv_path.'fin.csv', 'w+');
      fwrite($fp, "Артикул^Название товара^Цена^Описание^Производитель^Категория^Изображение^Мини изображение^Доступность товара^Валюта^Количество на складе^Статус публикации^COM_VIRTUEMART_FIELD_MECHANISM^COM_VIRTUEMART_FIELD_KALIBR^COM_VIRTUEMART_FIELD_POWER_RESERVE^COM_VIRTUEMART_FIELD_CASE^COM_VIRTUEMART_FIELD_BRACELET^COM_VIRTUEMART_FIELD_GLASS^COM_VIRTUEMART_FIELD_WATER_RESISTANCE^COM_VIRTUEMART_FIELD_BODY_SIZE^COM_VIRTUEMART_FIELD_DOCUMENTATION^COM_VIRTUEMART_FIELD_BOX^COM_VIRTUEMART_FIELD_CONDITION^COM_VIRTUEMART_FIELD_POL^COM_VIRTUEMART_FIELD_MATERIAL_ZASTEZHKI^COM_VIRTUEMART_FIELD_CIFERBLAT^COM_VIRTUEMART_FIELD_GOD^COM_VIRTUEMART_FIELD_CVET_CIFERBLATA^COM_VIRTUEMART_FIELD_ZASTEZHKA^COM_VIRTUEMART_FIELD_DIAMETR^COM_VIRTUEMART_FIELD_CIFRY^COM_VIRTUEMART_FIELD_PROCHEE^COM_VIRTUEMART_FIELD_SROK_DOSTAVKI^COM_VIRTUEMART_FIELD_VOZM_DOSTAVKI^COM_VIRTUEMART_FIELD_MODEL^COM_VIRTUEMART_FIELD_JUVTIP^COM_VIRTUEMART_FIELD_JUVMATERIAL^COM_VIRTUEMART_FIELD_JUVVSTAVKA^COM_VIRTUEMART_FIELD_JUVVES^COM_VIRTUEMART_FIELD_JUVRAZMER^COM_VIRTUEMART_FIELD_JUVKOROBKA^COM_VIRTUEMART_FIELD_JUVSERTIFIKAT^COM_VIRTUEMART_FIELD_JUVDOCS^COM_VIRTUEMART_FIELD_JUVKOLLEKCIYA^COM_VIRTUEMART_FIELD_JUVSOSTOYANIE^COM_VIRTUEMART_FIELD_JUVSROKI^COM_VIRTUEMART_FIELD_JUVDOST\n");
      fwrite($fp1, "Артикул^Название товара^Цена^Описание^Производитель^Категория^Изображение^Мини изображение^Доступность товара^Валюта^Количество на складе^Статус публикации^COM_VIRTUEMART_FIELD_MECHANISM^COM_VIRTUEMART_FIELD_KALIBR^COM_VIRTUEMART_FIELD_POWER_RESERVE^COM_VIRTUEMART_FIELD_CASE^COM_VIRTUEMART_FIELD_BRACELET^COM_VIRTUEMART_FIELD_GLASS^COM_VIRTUEMART_FIELD_WATER_RESISTANCE^COM_VIRTUEMART_FIELD_BODY_SIZE^COM_VIRTUEMART_FIELD_DOCUMENTATION^COM_VIRTUEMART_FIELD_BOX^COM_VIRTUEMART_FIELD_CONDITION^COM_VIRTUEMART_FIELD_POL^COM_VIRTUEMART_FIELD_MATERIAL_ZASTEZHKI^COM_VIRTUEMART_FIELD_CIFERBLAT^COM_VIRTUEMART_FIELD_GOD^COM_VIRTUEMART_FIELD_CVET_CIFERBLATA^COM_VIRTUEMART_FIELD_ZASTEZHKA^COM_VIRTUEMART_FIELD_DIAMETR^COM_VIRTUEMART_FIELD_CIFRY^COM_VIRTUEMART_FIELD_PROCHEE^COM_VIRTUEMART_FIELD_SROK_DOSTAVKI^COM_VIRTUEMART_FIELD_VOZM_DOSTAVKI^COM_VIRTUEMART_FIELD_MODEL^COM_VIRTUEMART_FIELD_JUVTIP^COM_VIRTUEMART_FIELD_JUVMATERIAL^COM_VIRTUEMART_FIELD_JUVVSTAVKA^COM_VIRTUEMART_FIELD_JUVVES^COM_VIRTUEMART_FIELD_JUVRAZMER^COM_VIRTUEMART_FIELD_JUVKOROBKA^COM_VIRTUEMART_FIELD_JUVSERTIFIKAT^COM_VIRTUEMART_FIELD_JUVDOCS^COM_VIRTUEMART_FIELD_JUVKOLLEKCIYA^COM_VIRTUEMART_FIELD_JUVSOSTOYANIE^COM_VIRTUEMART_FIELD_JUVSROKI^COM_VIRTUEMART_FIELD_JUVDOST\n");
      for($i = 0;$i < count($json);$i++){
        $result = '';
        $json[$i]['Reserve de marche'] = preg_replace('/[^0-9]/', '', $json[$i]['Reserve de marche']);
        $json[$i]['Condition'] = preg_replace('/[0-9() ]/', '', $json[$i]['Condition']);
        fwrite($fp1, $json[$i]['id'].'^'.
        $json[$i]['Name'].'^'.
        $json[$i]['Price'].'^'.
        $json[$i]['desc'].'^'.
        $json[$i]['Brand'].
        '^Sveitsin kellot^'.
        $json[$i]['img'].'^'.
        explode('|', $json[$i]['img'])[0].'^'.
        '1^USD^1'.
        '^1^'.'^'.
        '^'.
        '^'.
      '^'.
        '^'.
        '^^'.
        '^^^'.
        '^'.
        '^'.
        '^'.
      '^^^^'.
        '^'.
        '^'.
        '^^'.'^'.
      '^^^^^^^^^^^^'."\n");
        $keys = array_keys($json[$i]);
        for($j = 0;$j < count($keys);$j++){
          if($keys[$j] != 'Name' && $keys[$j] != 'desc' &&
           $keys[$j] != 'Price' && $keys[$j] != 'img' &&
           $keys[$j] != 'id' &&
           $keys[$j] != 'Brand'&&
           $keys[$j] != 'Type of mechanism'&&
           $keys[$j] != 'Caliber / Mechanism'&&
           $keys[$j] != 'Model'){
            if($json[$i][$keys[$j]] != preg_replace('/[а-ёяА-ёя]/', '', $json[$i][$keys[$j]])){
              $json[$i][$keys[$j]] = '';
            }else{
              $json[$i][$keys[$j]] = mb_strtolower($json[$i][$keys[$j]]);
            }
          }
        }
        fwrite($fp,
        $json[$i]['id'].'^'.
        $json[$i]['Name'].'^'.
        $json[$i]['Price'].'^'.
        $json[$i]['desc'].'^'.
        $json[$i]['Brand'].
        '^Swiss Watches^'.
        $json[$i]['img'].'^'.
        explode('|', $json[$i]['img'])[0].'^'.
        '1^USD^1'.
        '^1^'.
        $json[$i]['Type of mechanism'].'^'.
        $json[$i]['Caliber / Mechanism'].'^'.
        $json[$i]['Reserve de marche'].'^'.
        $json[$i]['Case material'].'^'.
        $json[$i]['Bracelet material'].'^'.
        $json[$i]['Glass'].'^^'.
        '^^^'.
        $json[$i]['Condition'].'^'.
        $json[$i]['Gender'].'^'.
        $json[$i]['Clasp material'].'^'.
        $json[$i]['Dial'].'^^^^'.
        $json[$i]['Diameter (mm)'].'^'.
        $json[$i]['Dial numbers'].'^'.
        $json[$i]['Other'].'^^'.$json[$i]['Availability'].'^'.
        $json[$i]['Model'].'^^^^^^^^^^^^'."\n");
      }
    }else{
      $fp = fopen($this->csv_path.'ru.csv', 'w+');
      fwrite($fp, "Артикул^Название товара^Цена^Описание^Производитель^Категория^Изображение^Мини изображение^Доступность товара^Валюта^Количество на складе^Статус публикации^COM_VIRTUEMART_FIELD_MECHANISM^COM_VIRTUEMART_FIELD_KALIBR^COM_VIRTUEMART_FIELD_POWER_RESERVE^COM_VIRTUEMART_FIELD_CASE^COM_VIRTUEMART_FIELD_BRACELET^COM_VIRTUEMART_FIELD_GLASS^COM_VIRTUEMART_FIELD_WATER_RESISTANCE^COM_VIRTUEMART_FIELD_BODY_SIZE^COM_VIRTUEMART_FIELD_DOCUMENTATION^COM_VIRTUEMART_FIELD_BOX^COM_VIRTUEMART_FIELD_CONDITION^COM_VIRTUEMART_FIELD_POL^COM_VIRTUEMART_FIELD_MATERIAL_ZASTEZHKI^COM_VIRTUEMART_FIELD_CIFERBLAT^COM_VIRTUEMART_FIELD_GOD^COM_VIRTUEMART_FIELD_CVET_CIFERBLATA^COM_VIRTUEMART_FIELD_ZASTEZHKA^COM_VIRTUEMART_FIELD_DIAMETR^COM_VIRTUEMART_FIELD_CIFRY^COM_VIRTUEMART_FIELD_PROCHEE^COM_VIRTUEMART_FIELD_SROK_DOSTAVKI^COM_VIRTUEMART_FIELD_VOZM_DOSTAVKI^COM_VIRTUEMART_FIELD_MODEL^COM_VIRTUEMART_FIELD_JUVTIP^COM_VIRTUEMART_FIELD_JUVMATERIAL^COM_VIRTUEMART_FIELD_JUVVSTAVKA^COM_VIRTUEMART_FIELD_JUVVES^COM_VIRTUEMART_FIELD_JUVRAZMER^COM_VIRTUEMART_FIELD_JUVKOROBKA^COM_VIRTUEMART_FIELD_JUVSERTIFIKAT^COM_VIRTUEMART_FIELD_JUVDOCS^COM_VIRTUEMART_FIELD_JUVKOLLEKCIYA^COM_VIRTUEMART_FIELD_JUVSOSTOYANIE^COM_VIRTUEMART_FIELD_JUVSROKI^COM_VIRTUEMART_FIELD_JUVDOST\n");
      for($i = 0;$i < count($json);$i++){
        $result = '';
        $json[$i]['Запас хода'] = preg_replace('/[^0-9]/', '', $json[$i]['Запас хода']);
        $json[$i]['Состояние'] = preg_replace('/[0-9() ]/', '', $json[$i]['Состояние']);
        fwrite($fp,
        $json[$i]['id'].'^'.
        $json[$i]['Name'].'^'.
        $json[$i]['Price'].'^'.
        $json[$i]['desc'].'^'.
        $json[$i]['Марка'].
        '^Швейцарские часы^'.
        $json[$i]['img'].'^'.
        explode('|', $json[$i]['img'])[0].'^'.
        '1^USD^1^1^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^'."\n");
      }
    }
  }
  public function buildSecond($json, $ru = false){
    if(!$ru){
      $fp = fopen($this->csv_path.'eng.csv', 'a');
      $fp1 = fopen($this->csv_path.'fin.csv', 'a');
      for($i = 0;$i < count($json);$i++){
        $result = '';
        $json[$i]['Body size:'] = preg_replace('/[^xх0-9]/','', $json[$i]['Body size:']);
        $json[$i]['Water resistance:'] = preg_replace('/[^0-9]/','', $json[$i]['Water resistance:']);
        $json[$i]['Power reserve:'] = preg_replace('/[^0-9]/','', $json[$i]['Power reserve:']);
        $json[$i]['Price'] = preg_replace('/[^0-9]/','', $json[$i]['Price']);
        fwrite($fp1, $json[$i]['id']."^".$json[$i]['Name']."^".$json[$i]['Price']."^".$json[$i]['desc']."^".$json[$i]['Brand'].'^Sveitsin kellot^'.$json[$i]['img'].'^'.explode('|',$json[$i]['img'])[0].'^1^USD^1^1^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^'.
        "\n");
        $keys = array_keys($json[$i]);
        for($j = 0;$j < count($keys);$j++){
          if($keys[$j] != 'Name' && $keys[$j] != 'desc' && $keys[$j] != 'Price' && $keys[$j] != 'img'
          && $keys[$j] != 'id' && $keys[$j] != 'id' && $keys[$j] != 'Mechanism:' && $keys[$j] != 'Калибр:' && $keys[$j] != 'Power reserve:' && $keys[$j] != 'Brand'){
            if($json[$i][$keys[$j]] != preg_replace('/[а-ёяА-ёя]/', '', $json[$i][$keys[$j]])){
              $json[$i][$keys[$j]] = '';
            }else{
              $json[$i][$keys[$j]] = mb_strtolower($json[$i][$keys[$j]]);
            }
          }
        }
        fwrite($fp, $json[$i]['id']."^".$json[$i]['Name']."^".$json[$i]['Price']."^".$json[$i]['desc']."^".$json[$i]['Brand'].'^Swiss Watches^'.$json[$i]['img'].'^'.explode('|',$json[$i]['img'])[0].'^1^USD^1^1^'.
        $json[$i]['Mechanism:'].'^'.
        $json[$i]['Калибр:'].'^'.
        $json[$i]['Power reserve:'].'^'.
        $json[$i]['Case:'].'^'.
        $json[$i]['Belt:'].'^'.
        $json[$i]['Glass:'].'^'.
        $json[$i]['Water resistance:'].'^'.
        $json[$i]['Body size:'].'^'.
        $json[$i]['Documentation:'].'^'.
        $json[$i]['Box:'].'^^^^^^^^^^^^^^^^^^^^^^^^^'.
        "\n");
      }
    }else{
      $fp = fopen($this->csv_path.'ru.csv', 'a');
      for($i = 0;$i < count($json);$i++){
        $json[$i]['Размер корпуса:'] = preg_replace('/[^xх0-9]/','', $json[$i]['Размер корпуса:']);
        $json[$i]['Водонепроницаемость:'] = preg_replace('/[^0-9]/','', $json[$i]['Водонепроницаемость:']);
        $json[$i]['Запас хода:'] = preg_replace('/[^0-9]/','', $json[$i]['Запас хода:']);
        $json[$i]['Price'] = preg_replace('/[^0-9]/','', $json[$i]['Price']);
        $result = '';
        fwrite($fp, $json[$i]['id']."^".$json[$i]['Name']."^".$json[$i]['Price']."^".$json[$i]['desc']."^".$json[$i]['Brand'].'^Швейцарские часы^'.$json[$i]['img'].'^'.explode('|',$json[$i]['img'])[0].'^1^USD^1^1^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^'."\n");
    }
  }
  }
  public function buildThird($json, $ru = false){
    if(!$ru){
      $fp = fopen($this->csv_path.'eng.csv', 'a');
      $fp1 = fopen($this->csv_path.'fin.csv', 'a');
      for($i = 0;$i < count($json);$i++){
        $result = '';
        $json[$i]['Condition'] = preg_replace('/[0-9() ]/', '', $json[$i]['Condition']);
        for($j = 0;$j < count($keys);$j++){
          if($json[$i][$keys[$j]] != preg_replace('/[а-ёяА-ёя]/', '', $json[$i][$keys[$j]])){
            $json[$i][$keys[$j]] = '';
          }
        }
        fwrite($fp1,
        $json[$i]['id'].'^'.$json[$i]['Name']
        .'^'.$json[$i]['Price'].'^'.$json[$i]['desc'].'^'
        .$json[$i]['Brand'].'^Korut^'.$json[$i]['img'].'^'
        .explode('|', $json[$i]['img'])[0].'^1^USD^1^1^^^^^^^^^^^^^^^^^^^^^^^'.'^^'.'^^^^^^^^'.'^^'."\n");
        $keys = array_keys($json[$i]);
        for($j = 0;$j < count($keys);$j++){
          if($keys[$j] != 'Name' && $keys[$j] != 'desc'
           && $keys[$j] != 'Price'
            && $keys[$j] != 'img' && $keys[$j] != 'id'&& $keys[$j] != 'Brand'&& $keys[$j] != 'Model'){
            if($json[$i][$keys[$j]] != preg_replace('/[а-ёяА-ёя]/', '', $json[$i][$keys[$j]])){
              $json[$i][$keys[$j]] = '';
            }else{
              $json[$i][$keys[$j]] = mb_strtolower($json[$i][$keys[$j]]);
            }
          }
        }
        fwrite($fp, $json[$i]['id'].'^'.
        $json[$i]['Name'].
        '^'.$json[$i]['Price'].
        '^'.$json[$i]['desc'].'^'.$json[$i]['Brand'].'^Jewelry^'.$json[$i]['img'].'^'.explode('|', $json[$i]['img'])[0].'^1^USD^1^1^^^^^^^^^^^^^^^^^^^^^^^'.$json[$i]['Model'].'^^'.$json[$i]['Case material'].'^^^^^^^^'.$json[$i]['Condition'].'^^'.$json[$i]['Availability']."\n");
      }
    }else{
      $fp = fopen($this->csv_path.'ru.csv', 'a');
      for($i = 0;$i < count($json);$i++){
        $result = '';
        $json[$i]['Состояние'] = preg_replace('/[0-9() ]/', '', $json[$i]['Состояние']);
        fwrite($fp, $json[$i]['id'].'^'
        .$json[$i]['Name']
        .'^'.
        $json[$i]['Price'].'^'
        .$json[$i]['desc'].'^'.
        $json[$i]['Марка'].'^Ювелирные украшения^'.
        $json[$i]['img'].'^'.
        explode('|', $json[$i]['img'])[0].'^'.
        '1^USD^1'.
        '^1^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^'."\n");
      }
    }
  }
  public function buildFourth($json, $ru = false){
    if(!$ru){
      $fp = fopen($this->csv_path.'eng.csv', 'a');
      $fp1 = fopen($this->csv_path.'fin.csv', 'a');
      for($i = 0;$i < count($json);$i++){
        $result = '';
        $json[$i]['Вес'] = preg_replace('/[^0-9]/', '', $json[$i]['Вес']);
        $json[$i]['Размер'] = preg_replace('/[^xх0-9]/','', $json[$i]['Размер']);
        fwrite($fp1, $json[$i]['id']."^".$json[$i]['Name']."^".$json[$i]['Price']."^".$json[$i]['desc']."^".$json[$i]['Brand'].'^Korut^'.$json[$i]['img'].'^'.explode('|', $json[$i]['img'])[0].'^1^USD^1^1^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^1'.
        "\n");
        $keys = array_keys($json[$i]);
        for($j = 0;$j < count($keys);$j++){
          if($keys[$j] != 'Name' && $keys[$j] != 'Desc' && $keys[$j] != 'Price' &&
           $keys[$j] != 'img'
           && $keys[$j] != 'id' && $keys[$j] != 'Вес' && $keys[$j] != 'Brand'){
            if($json[$i][$keys[$j]] != preg_replace('/[а-ёяА-ёя]/', '', $json[$i][$keys[$j]])){
              $json[$i][$keys[$j]] = '';
            }else{
              $json[$i][$keys[$j]] = mb_strtolower($json[$i][$keys[$j]]);
            }
          }
        }
        fwrite($fp, $json[$i]['id']."^".$json[$i]['Name']."^".$json[$i]['Price']."^".$json[$i]['desc']."^".$json[$i]['Brand'].'^Jewelry^'.$json[$i]['img'].'^'.explode('|', $json[$i]['img'])[0].'^1^USD^1^1^^^^^^^^^^^^^^^^^^^^^^^^'.
        $json[$i]['type'].'^'.
        $json[$i]['Материал'].'^'.
        $json[$i]['Вставка'].'^'.
        $json[$i]['Вес'].'^'.
        $json[$i]['Размер'].'^'.
        $json[$i]['Box'].'^^'.
        $json[$i]['Documentation'].'^^^^1'.
        "\n");
      }
    }else{
      $fp = fopen($this->csv_path.'ru.csv', 'a');
      for($i = 0;$i < count($json);$i++){
        $result = '';
        $json[$i]['Вес'] = preg_replace('/[^0-9,]/', '', $json[$i]['Вес']);
        $json[$i]['Размер'] = preg_replace('/[^xх0-9,]/','', $json[$i]['Размер']);
        fwrite($fp, $json[$i]['id']."^".$json[$i]['Name']."^".$json[$i]['Price']."^".$json[$i]['desc']."^".$json[$i]['Бренд'].'^Ювелирные украшения^'.$json[$i]['img'].'^'.explode('|', $json[$i]['img'])[0].'^1^USD^1^1^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^1'.
        "\n");
      }
    }
  }

  private function toLower(array $arr) : array{
    $keys = array_keys($arr);
    for($i = 0;$i< count($keys);$i++){
      $arr[$keys[$i]] = mb_strtolower($arr[$keys[$i]]);
    }
    return $arr;
  }

}
if($argv[1] != '601f46a69224f71dab8d03b5312f8e43') die;
$some = new HTMLtoCSV;
?>
