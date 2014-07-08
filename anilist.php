<?php

class Anilist {
    
    private $_version = 0.1;
    
    
    public function getSerie($serieId, $type = 'anime') {
        
        $html = $this->_getPage('http://anilist.co/' . $type . '/' . $serieId);

        $data = $this->_parsePage($html, array(
            'serieInfo' => '//div[@id="animeInfo"]/ul[1]/li',
            'serieDescription' => '//div[@id="animeDes"]/text()',
            'serieRelations' => '//div[@id="animeRel"]/span/div/a'
        ));

        if (isset($data['serieInfo'])) {
            
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
                'description' => trim($data['serieDescription']->item(1)->nodeValue),
                'image' => array(
                    'regular' => 'http://anilist.co/img/dir/anime/reg/' . $serieId . '.jpg',
                    'medium' => 'http://anilist.co/img/dir/anime/med/' . $serieId . '.jpg',
                    'small' => 'http://anilist.co/img/dir/anime/sml/' . $serieId . '.jpg'
                ),
                'type' => (isset($fields['Type:']) ? $fields['Type:'] : false),
                'status' => $fields['Status:'],
                'dateStart' => (isset($fields['Start:']) ? date('Y-m-d', strtotime($fields['Start:'])) : false),
                'dateEnd' => (isset($fields['End:']) ? date('Y-m-d', strtotime($fields['End:'])) : false),
                'genre' => (isset($fields['Genres:']) ? $fields['Type:'] : array()),
                'score' => floatval($fields['Average Score:'])
            );
        
            if ($type === 'manga') {
            
                $serieInfo['Volumes'] = (isset($fields['Volumes:']) ? intval($fields['Volumes:']) : false);
                $serieInfo['Chapters'] = (isset($fields['Chapters:']) ? intval($fields['Chapters:']) : false);
                $serieInfo['Serialized'] = (isset($fields['Serialized:']) ? $fields['Serialized:'] : false);
                
            }
            else {

                $serieInfo['episodes'] = (isset($fields['Episodes:']) ? intval($fields['Episodes:']) : false);
                $serieInfo['duration'] = (isset($fields['Duration:']) ? $fields['Duration:'] : false);
                $serieInfo['studio'] = (isset($fields['Main Work:']) ? $fields['Main Work:'] : false);
                $serieInfo['producers'] = (isset($fields['Producers:']) ? $fields['Producers:'] : false);
            
            }
            
            $serieInfo['related'] = array();
            
            if (isset($data['serieRelations'])) {

                foreach($data['serieRelations'] as $node) {

                    $href = $node->getAttribute('href');
                    $parts = explode('/', $href);

                    $serieInfo['related'][] = array(
                        'type' => $parts[1],
                        'id' => intval($parts[2])
                    );

                }
                
            }
            
        }
        else {
            
            $serieInfo = false;
            
        }
        
        return $serieInfo;
        
    }
    
    public function searchSerie($query, $type = 'anime') {
        
        $results = array();

        $html = $this->_getPage('http://anilist.co/getSearch.php?q=' . strtolower($query) . '&type=' . $type);

        $data = $this->_parsePage($html, array(
            'serieInfo' => '//a'
        ));
        
        if (isset($data['serieInfo'])) {

            foreach($data['serieInfo'] as $node) {

                $children = $node->getElementsByTagName('div');

                $href = $node->getAttribute('href');
                $parts = explode('/', $href);

                $results[] = array(
                    'name' => trim($children->item(0)->nodeValue),
                    'type' => $parts[1],
                    'id' => intval($parts[2])
                );
            
            }
            
        }
        
        return $results;
    
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