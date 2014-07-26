AniList-php
===================
Fetch serie information from [anilist.co](http://anilist.co/)

Uses curl for maximal network performance and xpath for dom traversal.

## Usage

```php
// Create a new instance
$anilist = new Anilist();

// Get serie info by id
$serieInfo = $anilist->getSerie(14751, 'anime');

var_dump($serieInfo);
```

Will output information as easily readable array
```
array(16) {
    ["id"]=> int(14751)
    ["name"]=> string(36) "Bishoujo Senshi Sailor Moon: Crystal"
    ["nameEnglish"]=> string(36) "Pretty Guardian Sailor Moon: Crystal"
    ["nameJapanese"]=> string(52) "美少女戦士セーラームーン クリスタル"
    ["nameSynonym"]=> array(3) {
        [0]=> string(33) "Pretty Soldier Sailor Moon (2014)"
        [1]=> string(18) "Sailor Moon (2014)"
        [2]=> string(34) "Bishoujo Senshi Sailor Moon (2014)"
    }
    ["image"]=> array(3) {
        ["regular"]=> string(45) "http://anilist.co/img/dir/anime/reg/14751.jpg"
        ["medium"]=> string(45) "http://anilist.co/img/dir/anime/med/14751.jpg"
        ["small"]=> string(45) "http://anilist.co/img/dir/anime/sml/14751.jpg"
    }
    ["type"]=> string(3) "ONA"
    ["episodes"]=> int(26)
    ["duration"]=> bool(false)
    ["status"]=> string(16) "currently airing"
    ["dateStart"]=> string(10) "2014-07-05"
    ["dateEnd"]=> bool(false)
    ["studio"]=> string(14) "Toei Animation"
    ["producers"]=> array(1) {
        [0]=> string(14) "Toei Animation"
    }
    ["genre"]=> array(4) {
        [0]=> string(6) "Demons"
        [1]=> string(5) "Magic"
        [2]=> string(7) "Romance"
        [3]=> string(6) "Shoujo"
    }
    ["score"]=> float(74.7)
    ["related"]=> array(2) {
        [0]=> array(2) {
            ["type"]=> string(5) "manga"
            ["id"]=> int(92)
        }
        [1]=> array(2) {
            ["type"]=> string(5) "anime"
            ["id"]=> int(530)
        }
    }
}
```

You can also search series
```php
$series = $anilist->searchSerie('Onegai', 'anime');

var_dump($series);
```

Outputs
```
array(2) {
    [0]=> array(3) {
        ["name"]=> string(16) "Onegai My Melody"
        ["type"]=> string(5) "anime"
        ["id"]=>  int(2489)
    }
    [1]=> array(3) {
        ["name"]=> string(26) "Onegai My Melody Kirara☆"
        ["type"]=> string(5) "anime"
        ["id"]=>  int(5938)
    }
}
```