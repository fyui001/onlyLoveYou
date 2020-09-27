<?php
$str = '松井恵理子';
$options = ['-d', '/usr/local/lib/mecab/dic/mecab-ipadic-neologd'];
$mecab = new Mecab\Tagger($options);
$node = $mecab->parseToNode($str);
$result = '';
foreach ($node as $n) {
    $item = $n->getNext();
    $result .= $n->getStat() != 2 && $n->getStat() != 3 && mb_strpos($n->getFeature(), '人名') ? $n->getSurface() : false;
}

echo $result;