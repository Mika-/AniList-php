<?php

class Anilist {
    
    private $_version = 0.1;
    
    
    public function getSerie($serieId, $type = 'anime') {
        
        $html = $this->_getPage('http://anilist.co/' . $type . '/' . $serieId);

        $data = $this->_parsePage($html, array(
            'serieInfo' => '//div[@id="animeInfo"]/ul[1]/li',
            'serieImage' => '//img[@class="poster"]/@src'
        ));

        if (count($data) > 0) {
            
            $fields = array();

            $currentField = false;
            foreach($data['serieInfo'] as $node) {
                
                $children = $node->getElementsByTagName('span');
                
                if (($currentField === false || $currentField !== $children->item(0)->nodeValue) && !empty($children->item(0)->nodeValue))
                    $currentField = $children->item(0)->nodeValue;
                
                if ($currentField === 'Genres:' || $currentField === 'Producers:') {
                
                    $currentValue = array();
                    
                    foreach($children->item(1)->childNodes as $node) {
                    
                        if ($node->nodeName !== 'br')
                            $currentValue[] = $node->nodeValue;
                    
                    }
                
                }
                else {
                    
                    $currentValue = trim($children->item(1)->nodeValue);
                
                }

                if (!isset($fields[$currentField])) {
                    
                    $fields[$currentField] = $currentValue;
                    
                }
                else if (!is_array($fields[$currentField])) {
                    
                    $fields[$currentField] = array(
                        $fields[$currentField],
                        $currentValue
                    );
                    
                }
                else {
                    
                    $fields[$currentField][] = $currentValue;
                    
                }

            }
            
            $serieInfo = array(
                'id' => $serieId,
                'name' => $fields['Romaji Title:'],
                'nameEnglish' => (isset($fields['Eng Title:']) ? $fields['Eng Title:'] : false),
                'nameJapanese' => (isset($fields['Japanese:']) ? $fields['Japanese:'] : false),
                'nameSynonym' => $fields['Synonym:'],
                'image' => 'http://anilist.co' . trim($data['serieImage']->item(0)->nodeValue),
                'type' => (isset($fields['Type:']) ? $fields['Type:'] : false),
                'episodes' => (isset($fields['Episodes:']) ? $fields['Episodes:'] : false),
                'duration' => (isset($fields['Duration:']) ? $fields['Duration:'] : false),
                'status' => $fields['Status:'],
                'start' => (isset($fields['Start:']) ? $fields['Start:'] : false),
                'end' => (isset($fields['End:']) ? $fields['End:'] : false),
                'studio' => (isset($fields['Main Work:']) ? $fields['Main Work:'] : false),
                'producers' => $fields['Producers:'],
                'genre' => $fields['Genres:'],
                'score' => floatval($fields['Average Score:'])
            );
        
        }
        else {
            $serieInfo = false;
        }
        
        return $serieInfo;
        
    }

    private function _parsePage($html, $selectors = array()) {
        
        $document = new DOMDocument();
        $results = array();
        
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'); 

        @$document->loadHTML($html);
        
        $xpathDocument = new DomXPath($document);
        
        foreach($selectors as $key => $selector) {
        
            $nodes = $xpathDocument->query($selector);
            
            if ($nodes->length > 0)
                $results[$key] = $nodes;

        }
        
        return $results;
        
    }
    
    private function _getPage($url) {
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (rv:' . $this->_version . ') AniList-php/' . $this->_version);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    
    }

}