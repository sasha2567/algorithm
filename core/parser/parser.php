<?php
include 'core/simple_html_dom.php';
 
class parse
{
	var $getCurrentParseUrl = 'https://liberty-tattoo.ru/gallery/tatu-foto/';
	var $getRootUrlForImage = 'https://liberty-tattoo.ru';
	var $html = nil;
	
	// массив всех URL сайта
	var $getImageArray = array();

	public function __construct()
	{
		echo '<p>Старт парсинга</p>';
	}
	
	public function run($page = 1)
	{
		$count = 1;
		while ($count <= $page) {
			 $url = $this->getUrlByPage($count);

			// дерним html
			$html = $this->tweak($url);

			//получаем все картинки со страницы
			$this->getImages($html);
			$count++;
		}
	}

	//получить структуру dom страницы
	public function tweak($url = '')
	{
		return file_get_html($url);
	}

	// получить все ссылки на странице
	public function getUrlByPage($page)
	{
		if (!empty($page) && $page != 1) {
			return $this->getCurrentParseUrl."?PAGEN_1=".$page;
		} else {
			return $this->getCurrentParseUrl;
		}
	}

	// проверим принадлежит ли ссылка к нашему сайту 
	function getImages($html = nil)
	{
		$divs = $html->find('div.gallery_container .photo-container');
		foreach ($divs as $div) {
			$a = $div->find('a');
			$href = $a[0]->attr['href'];
			$name = explode('/', $href);
			end($name);
			$lastKey = key($name);
			copy($this->getRootUrlForImage.$href,"save_dir/".$name[$lastKey]);
		}
	}
}
