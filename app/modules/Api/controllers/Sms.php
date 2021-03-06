<?php
/**
 * @author ciogao@gmail.com
 * Date: 13-7-1 上午11:36
 */
class SmsController extends Controller
{

    /**
     * @var models_smsfeed
     */
    private $model_sms_feed;

    /**
     * @var models_msg
     */
    private $model_msg;

    public function init()
    {
        parent::init();
        $this->model_sms_feed = models_smsfeed::getInstance();
        $this->model_msg      = models_msg::getInstance();
    }

    /**
     * feed气泡
     */
    public function bubblesAction()
    {
        $this->rest->method('POST');
        $data['bubbles'] = $this->model_sms_feed->bubbles($this->user_id);
        $this->rest->success($data);
    }

    /**
     * feed列表
     * @todo start limit 格式
     */
    public function feedAction()
    {
        $this->rest->method('GET');

        $this->getStartLimit();
        $info = $this->model_sms_feed->myFeed($this->user_id, $this->start, $this->limit);

        $this->mkData->setOffset($this->start, $this->limit);
        $this->mkData->config(count($info), 'feed_id');

        $info = spall_reply::mkDataForSmsList($info);

        $data = $this->mkData->make($info);
        $this->rest->success($data);
    }

    /**
     *已读feed
     */
    public function readfeedAction()
    {
        $this->rest->method('POST');

        $params = $this->allParams();

        $this->rest->paramsMustMap = array('feed_id');
        $this->rest->paramsMustValid($params);

        $info = $this->model_sms_feed->readFeed($params['feed_id']);

        if ($info == FALSE) $this->rest->error(rest_Code::STATUS_SUCCESS_DO_ERROR_DB);

        $this->rest->success();
    }

    /**
     * 忽略、删除feed
     */
    public function removefeedAction()
    {
        $this->rest->method('POST');

        $params = $this->allParams();

        $this->rest->paramsMustMap = array('feed_id');
        $this->rest->paramsMustValid($params);

        $info = $this->model_sms_feed->removeFeed($params['feed_id']);

        if ($info == FALSE) $this->rest->error(rest_Code::STATUS_SUCCESS_DO_ERROR_DB);

        $this->rest->success();
    }

    /**
     * feed詳情
     * @todo
     */
    public function detailAction()
    {
        $this->rest->method('GET');

        $params = $this->allParams();

        $this->rest->paramsMustMap = array('feed_id');
        $this->rest->paramsMustValid($params);

        $this->model_sms_feed->readFeed($params['feed_id']);

        $this->getStartLimit();
        $info = $this->model_msg->getMsgsByFeedid($this->user_id, $params['feed_id'], $this->start, $this->limit);

        $info = spall_reply::mkDataForSmsList($info);

        $this->mkData->setOffset($this->start, $this->limit);
        $this->mkData->config(count($info), 'msg_id');
        $data = $this->mkData->make($info);

        $this->rest->success($data);
    }

    /**
     *发送\回复 短消息
     * ＠todo 返回格式化 user_id_to
     */
    public function sendAction()
    {
        $this->rest->method('POST');

        $params = $this->allParams();

        $this->rest->paramsMustMap = array('content');
        $this->rest->paramsMustValid($params);

        if (!array_key_exists('feed_id', $params) && (int)$params['feed_id'] < 1) {
            $params['feed_id'] = $this->model_sms_feed->createFeed($params['to_user_id'], $params['content']);
        }

        $result = $this->model_msg->send($params['feed_id'], $params['to_user_id'], $params['content']);
        if ($result) {
            $data[] = array(
                'feed_id'      => $params['feed_id'],
                'user_id_from' => $this->user_id,
                'content'      => $params['content'],
                'dateline'     => time(),
            );

            $info = spall_reply::mkDataForSmsList($data);
            models_smsfeed::getInstance()->sendSms($params['feed_id']);

            $this->rest->success($info[0], '', '发送成功');
        }

        $this->rest->error();
    }

    /**
     * 短消息列表
     */
    public function showAction()
    {

    }

    /**
     * 删除短消息
     */
    public function delAction()
    {

    }
}