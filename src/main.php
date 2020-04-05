<?php
class main
{
    /**
     * データベース接続
     *
     * @return PDO
     */
    protected function getDb() {

        $conf = parse_ini_file(__DIR__ . '/../config.ini', true);
        try{
            $db = new PDO("mysql:dbname={$conf['DATABASE']['DB_NAME']}; host={$conf['DATABASE']['HOST']}; charset=utf8;", $conf['DATABASE']['USER'], $conf['DATABASE']['PASSWD']);
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
    public function create(array $request) {

        if ( $this->seiyuNameAnalysis($request['msg']) ) {

            try {

                $voiceActorName = $this->seiyuNameAnalysis($request['msg']);
                $db = $this->getDb();
                $tableName = 'only_love_you';
                $sql = "INSERT INTO `{$tableName}` (user, content, love, guild) VALUES (:usr, :msgContent, :seiyuName, :guildName) ";
                $stt = $db->prepare($sql);
                $stt->bindValue(':usr', $request['usr']);
                $stt->bindValue(':msgContent', $request['msg']);
                $stt->bindValue(':seiyuName', $voiceActorName);
                $stt->bindValue(':guildName', $request['guild']);
                $stt->execute();

            } catch(PDOException $e) {
                $db->rollBack();
                die( $e->getMessage() );
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
    public function create(array $request) {

        if ( $this->seiyuNameAnalysis($request['msg']) ) {
            try {
                $db = $this->getDb();
                $voiceActorName = $this->seiyuNameAnalysis($request['msg']);
                $tableName = 'voice_actor_ownership';
                $sql = "INSERT INTO `{$tableName}` (user, content, claim_ownership, guild) VALUES (:usr, :msgContent, :voiceActorName, :guildName )";
                $stt = $db->prepare($sql);
                $stt->bindValue(':usr', $request['usr']);
                $stt->bindValue(':msgContent', $request['msg']);
                $stt->bindValue(':voiceActorName', $voiceActorName);
                $stt->bindValue(':guildName', $request['guild']);
                $stt->execute();
            } catch(PDOException $e) {
                $db->rollBack();
                die( $e->getMessage() );
            }
        }

    }
}

class OnlyYouWin extends main
{
    public function create(array $request) {
        if ( $this->seiyuNameAnalysis($request['msg']) ) {

            try {
                $db = $this->getDb();
                $voiceActorName = $this->seiyuNameAnalysis($request['msg']);
                $tableName = 'voice_actor_ownership';
                $sql = "INSERT INTO `{$tableName}` (user, content, win, guild) VALUES (:usr, :msgContent, :voiceActorName, :guildName )";
                $stt = $db->prepare($sql);
                $stt->bindValue(':usr', $request['usr']);
                $stt->bindValue(':msgContent', $request['msg']);
                $stt->bindValue(':voiceActorName', $voiceActorName);
                $stt->bindValue(':guildName', $request['guild']);
                $stt->execute();
            } catch(PDOException $e) {
                $db->rollBack();
                die( $e->getMessage() );
            }

        }
    }
}
