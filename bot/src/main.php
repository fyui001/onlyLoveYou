<?php
require_once(__DIR__ . '/../vendor/autoload.php');

use src\Database;

class Main
{
    /**
     * 形態素解析で声優の名前を解析
     *
     * @param string $input
     * @return string
     */
    public function seiyuNameAnalysis(string $input): string {

        try {
            $options = ['-d', '/usr/local/lib/mecab/dic/mecab-ipadic-neologd'];
            $mt = new MeCab\Tagger($options);
            $node = $mt->parseToNode($input);
            $result = '';
            foreach ($node as $n) {
                $result .= $n->getStat() != 2 && $n->getStat() != 3 && mb_strpos($n->getFeature(), '人名') ? $n->getSurface() : false;
            }
            return $result;
        } catch (Exception $e) {
            die($e->getMessage());
        }

    }

}

class OnlyLoveYou extends Main
{
    /**
     * 送信者とメッセージ内容と解析した名前をデータベースに追加する
     *
     * @param array $request
     * @return void
     */
    public function create(array $request) {

        if ( $this->seiyuNameAnalysis($request['msg']) ) {

            try {
                $voiceActorName = $this->seiyuNameAnalysis($request['msg']);
                $database = new Database();
                $db = $database->getDb();
                $tableName = 'only_love_you';
                $sql = "INSERT INTO `{$tableName}` (user, content, love, guild) VALUES (:usr, :msgContent, :seiyuName, :guildName) ";
                $stt = $db->prepare($sql);
                $stt->bindValue(':usr', $request['usr']);
                $stt->bindValue(':msgContent', $request['msg']);
                $stt->bindValue(':seiyuName', $voiceActorName);
                $stt->bindValue(':guildName', $request['guild']);
                $stt->execute();
            } catch(PDOException $e) {
                echo "残念";
                $db->rollBack();
                die( $e->getMessage() );
            }

        } else {
            echo  $this->seiyuNameAnalysis($request['msg']);
        }

    }

}

class VoiceActorOwnership extends Main
{
    /**
     * 声優の所有権の主張を検知して解析して保存する
     *
     * @param array $request
     * @return bool|void
     */
    public function create(array $request) {

        if ( $this->seiyuNameAnalysis($request['msg']) ) {
            try {
                $database = new Database();
                $db = $database->getDb();
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

class OnlyYouWin extends Main
{

    /**
     * 勝利宣言
     *
     * @param array $request
     */
    public function create(array $request) {

        if ( $this->seiyuNameAnalysis($request['msg']) ) {

            try {
                $database = new Database();
                $db = $database->getDb();
                $voiceActorName = $this->seiyuNameAnalysis($request['msg']);
                $tableName = 'only_you_win';
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
