<?php
class main
{
    /**
     * データベース接続
     *
     * @return PDO
     */
    protected function getDb() {
        $user = '';
        $passwd = '';
        $host = '';
        $dbName = '';

        try{
            $db = new PDO("mysql:dbname={$dbName}; host={$host}; charset=utf8;", $user, $passwd);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch(PDOException $e) {
            die("接続に失敗{$e->getMessage()}");
        }
        return $db;
    }

    /**
     * 送信者とメッセージ内容と解析した名前をデータベースに追加する
     *
     * @param $usr
     * @param $msgContent
     * @return bool
     */
    public function create($usr, $msgContent) {

        if ( $seiyuName = $this->seiyuNameAnalysis($msgContent)) {

            try {
                $db = $this->getDb();
                $tableName = 'onlyLoveYou';
                $sql = "INSERT INTO `{$tableName}` (UserName, Content, Love) VALUES ('{$usr}', '{$msgContent}', '{$seiyuName}') ";
                echo "{$sql}\n";
                $stt = $db->prepare($sql);
                $stt->execute();
                return true;
            } catch (Exception $e) {
                die($e->getMessage());
            }

        }
    }

    /**
     * 形態素解析で声優の名前を解析
     *
     * @param $input
     * @return string
     */
    public function seiyuNameAnalysis($input) {

        $options = ['-d', '/usr/lib64/mecab/dic/mecab-ipadic-neologd/'];
        $mt = new MeCab\Tagger($options);
        $result = '';
        for ($node = $mt->parseToNode($input); $node; $node = $node->getNext()) {
            $result .= $node->getStat() != 2 && $node->getStat() != 3 && mb_strpos($node->getFeature(), '人名') ? $node->getSurface() : false;
        }
        return $result;
    }

}
