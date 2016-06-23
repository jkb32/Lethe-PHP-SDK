<?php
namespace Lethe;
class Lethe {
    private $pKey;
    private $oKey;
    private $apiUrl;

    protected $lastError;

    protected $debug = false;

    /**
     * Lethe constructor.
     * @param string $apiUrl url of lethe.api.php
     * @param string $pKey organization public key
     * @param string $oKey organization API key
     */
    public function __construct( $apiUrl, $pKey, $oKey ) {
        $this->apiUrl = $apiUrl;
        $this->pKey = $pKey;
        $this->oKey = $oKey;
    }

    /**
     * Add new subscriber
     * @param string $mail email address
     * @param null|string $groupId group id
     * @param null|string $name subscriber name
     *
     * @return bool|array
     */
    public function add($mail, $groupId = null, $name = null ) {
        return $this->_exec([
            'act' => 'add',
            'pkey' => $this->pKey,
            'akey' => $this->oKey,
            'lmail' => $mail,
            'lgrp' => $groupId,
            'lsname' => $name
        ]);
    }

    /**
     * Delete subscriber
     * @param string $mail mail to delete
     *
     * @return bool
     */
    public function delete($mail) {
        return $this->_exec([
            'act' => 'remove',
            'pkey' => $this->pKey,
            'akey' => $this->oKey,
            'lmail' => $mail
        ]);
    }

    /**
     * Change subscriber e-mail
     * @param string $oldMail Old e-mail address
     * @param string $newMail New e-mail address
     * @param null|int $groupId Group id
     * @param null|string $name Subscriber name
     *
     * @return bool
     */
    public function change($oldMail, $newMail, $groupId = null, $name = null) {
        if($this->delete($oldMail)) {
            return $this->add($newMail, $groupId, $name);
        } else return false;
    }

    public function getLastError() {
        return $this->lastError;
    }

    protected function _exec( $params ) {
        if($this->debug) {
            echo($this->apiUrl . "?" . http_build_query($params)."<br>");
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . "?" . http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($output);

        if(!$output->success) $this->lastError = $output->error;

        return isset($output->success) ? $output->success : false;
    }
}
?>