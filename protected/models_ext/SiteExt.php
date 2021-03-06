<?php 
/**
 * 站点配置类
 * 数据结构为 name为 qjpz value为 属性分类的key-value组成的json数据
 * @author steven.allen <[<email address>]>
 * @date(2017.2.13)
 */
class SiteExt extends Site{

    // 属性
    public static $cates = [
        // 业务部联系方式
        'bussiness_tel'=>'',
        'map_lng'=>'',
        'map_lat'=>'',
        'map_zoom'=>'',
        'sen'=>'',
    ];
    public static $cateName = [
        'qjpz' => '全局配置',
        'sen'=>'敏感词配置',
    ];

    // 属性分类
    public static $cateTag = [
        'qjpz'=> [
            // 'pcIndexImages'=>['type'=>'multiImage','max'=>5,'name'=>'pc首页轮播图'],
            // 'pcLogo'=>['type'=>'image','max'=>1,'name'=>'pc版logo'],
            // 'sitePhone'=>['type'=>'text','name'=>'站点客服'],
            'bussiness_tel'=>['type'=>'text','name'=>'业务部联系方式'],
            'map_lng'=>['type'=>'text','name'=>'默认经度'],
            'map_lat'=>['type'=>'text','name'=>'默认纬度'],
            'map_zoom'=>['type'=>'text','name'=>'默认缩放值'],
            // 'pcIndexGun'=>['type'=>'text','name'=>'pc首页小喇叭'],

            // 'wxgzh'=>['type'=>'text','name'=>'微信公众号'],
            // 'xmgs'=>['type'=>'text','name'=>'项目个数'],
            // 'clnf'=>['type'=>'text','name'=>'成立年份'],
            // 'pcContact'=>['type'=>'image','max'=>1,'name'=>'pc联系我们头图'],
            // 'wx_img'=>['type'=>'image','max'=>1,'name'=>'微信公众号二维码'],
            // 'leagueApi'=>['type'=>'text','name'=>'联赛调用接口地址'],
            // 'teamApi'=>['type'=>'text','name'=>'球队调用接口地址'],
            // 'playerApi'=>['type'=>'text','name'=>'球员调用接口地址'],
            // 'matchApi'=>['type'=>'text','name'=>'比赛调用接口地址'],
            // 'newsApi'=>['type'=>'text','name'=>'资讯调用接口地址'],
            // 'pcGsjs'=>['type'=>'image','max'=>1,'name'=>'pc公司介绍头图'],
            // 'pcLxwm'=>['type'=>'image','max'=>1,'name'=>'pc联系我们广告图'],
            // 'pcIndexAbout'=>['type'=>'image','max'=>1,'name'=>'pc首页关于背景图'],
            // 'pcIndexServe'=>['type'=>'image','max'=>1,'name'=>'pc首页服务背景图'],
            // 'pcNewsTop'=>['type'=>'image','max'=>1,'name'=>'pc资讯列表头图'],
            // 'pcContactTop'=>['type'=>'image','max'=>1,'name'=>'pc联系列表头图'],
            // 'userImg'=>['type'=>'image','max'=>1,'name'=>'用户默认头像'],
            // 'newsImg'=>['type'=>'image','max'=>1,'name'=>'资讯默认封面图'],
            // 'headCode'=>['type'=>'textarea','name'=>'头部代码'],
            // 'footCode'=>['type'=>'textarea','name'=>'底部代码'],
            // 'about'=>['type'=>'textarea','name'=>'关于我们'],
            // 'contact'=>['type'=>'textarea','name'=>'联系我们'],
            // 'pcTeamTop'=>['type'=>'image','max'=>1,'name'=>'pc团队列表头图'],
            // 'productNoPic'=>['type'=>'image','max'=>1,'name'=>'产品默认图'],
            // 'houseNoPic'=>['type'=>'image','max'=>1,'name'=>'酒庄默认图'],
            ],
        'sen'=>[
            'sen'=>['type'=>'text','name'=>'敏感词'],
        ],
    ];

	/**
     * 定义关系
     */
    public function relations()
    {
        return array(
            // 'baike'=>array(self::BELONGS_TO, 'BaikeExt', 'bid'),
        );
    }

    /**
     * @return array 验证规则
     */
    public function rules() {
        $rules = parent::rules();
        return array_merge($rules, array(
            array(implode(",", array_keys(self::$cates)) ,'safe'),
        ));
    }

    /**
     * 返回指定AR类的静态模型
     * @param string $className AR类的类名
     * @return CActiveRecord Admin静态模型
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function afterFind() {
        parent::afterFind();
    }

    public function beforeValidate() {
        if($this->getIsNewRecord())
            $this->created = $this->updated = time();
        else
            $this->updated = time();
        return parent::beforeValidate();
    }

    /**
     * 命名范围
     * @return array
     */
    public function scopes()
    {
        $alias = $this->getTableAlias();
        return array(
            'sorted' => array(
                'order' => 'sort desc',
            )
        );
    }

    // 重写get魔术方法
    public function __get($value)
    {
        if(in_array($value, array_keys(self::$cates))) {
            $dc = json_decode($this->value,true);
            if($dc && isset($dc[$value])) {
                return $dc[$value];
            }
        } else {
            return parent::__get($value);
        }
    }

    // 重写set魔术方法
    public function __set($name, $value)
    {
        if(isset(self::$cates[$name])) {
            if(is_array($this->value))
                $data_conf = $this->value;
            else
                $data_conf = CJSON::decode($this->value);
            self::$cates[$name] = $value;
            $data_conf[$name] = $value;
            $this->value = json_encode($data_conf);
        }
        else
            parent::__set($name, $value);
    }

    /**
     * 通过name获取
     */
    public function getSiteByCate($cate)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'name=:cate',
            'order' => 'id ASC',
            'params' => array(':cate'=>$cate)
        ));
        return $this;
    }

    /**
     * [getAttr 获取配置]
     * @param  string $cate [类别]
     * @param  string $attr [属性]
     * @return [type]       [description]
     */
    public static function getAttr($cate='',$attr='')
    {
        if(!in_array($attr, array_keys(SiteExt::$cates)))
            return '';
        $model = self::model()->getSiteByCate($cate)->find();
return isset($model)&&$model->$attr?$model->$attr:'';
    }

}