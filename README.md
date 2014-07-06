AniList-php
===================
Fetch serie information from [anilist.co](https://anilist.co/)

Uses curl for maximal network performance and xpath for dom traversal.

## Usage

```php
// Create a new instance
$anilist = new Anilist();

// Get serie info by id
$serieInfo = $anilist->getSerie(14751);

var_dump($serieInfo);
```

Will output
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
    ["image"]=> string(45) "http://anilist.co/img/dir/anime/reg/14751.jpg"
    ["type"]=> string(3) "ONA"
    ["episodes"]=> string(2) "26"
    ["duration"]=> bool(false)
    ["status"]=> string(16) "currently airing"
    ["start"]=> string(12) "Jul 05, 2014"
    ["end"]=> bool(false)
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
}
```