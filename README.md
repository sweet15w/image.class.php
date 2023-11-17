# image.class.php

Class to work with images

## Including

```php
require_once "image.class.php";
```

## Init

```php	
$image = new Image("file_path");
```

## Params

```php
// width - длина сохраняемого изображения ( по умолчанию 150 )
// height - высота сохраняемого изображения ( по умолчанию 150 )
// path - директория сохранения изображения ( по умолчанию "../uploadfiles/images/" )
// name - имя сохраняемого изображения ( по умолчанию strtotime("now")."-".rand( 1000000000, 9999999999 ) )
// quality - качество сохраняемого изображения ( по умолчанию 100 )
// size - способ обработки изображения ( по умолчанию proportion, возможные значения: 
    // proportion ( уменьшить пропорционально ), 
	// fixed ( фиксированный размер ), 
	// crop ( обрезать лишнее ), 
	// noresize ( размер оригинала ) )
	// square ( квадратная картинка по меньшей стороне обрезана )
// watermark - водяной знак на изображение ( по умолчанию отсутствует )
// prefix - дополнительная надпись в имени изображения перед названием ( по умолчанию отсутствует )
// результатом выполнения метода является имя файла сохраненного изображения
```

## Examples

```php
$resourse = $image->save([ 
  'width' => 150, 
  'height' => 150, 
  'path' => '../uploadfiles/images/', 
  'name' => 'image.jpg', 
  'quality' => 100, 
  'size' => 'crop', 
  'watermark' => 'images/watermark.jpg',
  'prefix' => 's'
]);
```
