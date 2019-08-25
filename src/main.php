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
     * 形態素解析で声優の名前を解析
     *
     * @param $input
     * @return string
     */
    public function seiyuNameAnalysis($input) {

        $options = ['-d', '/usr/lib64/mecab/dic/mecab-ipadic-neologd/'];
        $mt = new MeCab\Tagger($options);
        $result = '';

        for ( $node = $mt->parseToNode($input); $node; $node = $node->getNext() ) {
            $result .= $node->getStat() != 2 && $node->getStat() != 3 && mb_strpos($node->getFeature(), '人名') ? $node->getSurface() : false;
        }
        return $result;
    }

}

class OnlyLoveYou extends main
{
    /**
     * 送信者とメッセージ内容と解析した名前をデータベースに追加する
     *
     * @param $usr
     * @param $msgContent
     * @return bool
     */
    public function create($usr, $msgContent) {

        if ( $seiyuName = $this->seiyuNameAnalysis($msgContent) ) {

            try {

                $db = $this->getDb();
                $tableName = 'only_love_you';
                $sql = "INSERT INTO `{$tableName}` (UserName, Content, Love) VALUES ('{$usr}', '{$msgContent}', '{$seiyuName}') ";

                $stt = $db->prepare($sql);
                $stt->execute();
                return true;

            } catch (Exception $e) {
                die($e->getMessage());
            }

        }

    }

}

class VoiceActorOwnership extends main
{
    /**
     * 声優の所有権の主張を検知して解析して保存する
     *
     * @param $usr
     * @param $msgContent
     * @return bool|void
     */
    public function create($usr, $msgContent) {

        if ( $voiceActorName = $this->seiyuNameAnalysis($msgContent) ) {

            try {
                $db = $this->getDb();
                $tableName = 'voice_actor_ownership';
                $sql = "INSERT INTO `{$tableName}` (UserName, Content, ClaimOwnership) VALUES ('{$usr}', '{$msgContent}', '{$voiceActorName}' )";

                $stt = $db->prepare($sql);
                $stt->execute();
                return true;

            } catch(PDOException $e) {
                die($e->getMessage());
            }
        }

    }

}
