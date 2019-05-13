个人使用文档
	一键导出：使用标签excel生成[导出]按钮
		\application\admin\traits\controller\Controller.php:23行
		坑：低版本中PHPExcel\Calculation\Functions.php文件，加上581行的break即可
	    	高版本中PHPExcel\Calculation\Functions.php文件，删除581行的break即可
	    	if ($this->excel) {
	            $this->excel = [];
	            $this->excel['header'] = [
	                '姓名',
	                '电话',
	                '举报类型',
	                '内容',
	                '上报位置',
	                '事发位置',
	                '举报时间',
	                '有无附件',
	            ];
	            $this->excel['field'] = function($row) {
	                $row['r_have'] = '';
	                if ($row['r_have_image']) {
	                    $row['r_have'] .= '有照片';
	                }
	                if ($row['r_have_video']) {
	                    $row['r_have'] .= '有视频';
	                }
	                return [
	                    $row['r_name'],
	                    $row['r_phone'],
	                    $row['t_name'],
	                    $row['r_content'],
	                    $row['r_longitude'] . ',' . $row['r_latitude'],
	                    $row['r_fa_longitude'] . ',' . $row['r_fa_latitude'],
	                    date('Y-m-d', $row['create_time']),
	                    $row['r_have'],
	                ];
	            };
	            $this->excel['name'] = '举报信息';
	        }


	下拉自动选择: $("[name='type']").find("[value='{$vo.type ?? ''}']").attr("selected", true);

	时间插件：当前input框添加 onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" 
	         添加 <script src="__STATIC__/lib/My97DatePicker/WdatePicker.js"></script>

	         <td>{$vo.f_addtime|date='Y-m-d H:i:s',###}</td>

	         <input type="text" class="input-text" placeholder="结束时间" name="end_time" value="{if condition="!empty($vo['end_time'])"}{$vo.end_time|date='Y-m-d H:i:s', ###}{/if}" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})">

	上传文件：历史图片可以选择多张图片  已修改
			<button type="button" class="btn btn-primary radius" onclick="layer_open('文件上传','{:\\think\\Url::build(\'Upload/index\', [\'id\' => \'upload\'])}')">上传</button>

	上传验证： //验证后缀
			$info = $file->validate(['ext'=>'jpg,png,jpeg,gif'])->move($path);

	编辑器  ：上传视频可正常播放         已修改
			<div class="row cl">
			    <label class="form-label col-xs-3 col-sm-3" style="width:10%;"><span class="c-red">*</span>内容：</label>
			    <div class="formControls col-xs-6 col-sm-6">
			        <div>
			            <script id="editor" name="n_content" type="text/plain" style="height:400px;width:600px;">
			                <?php if (!empty($vo['n_content'])) {echo htmlspecialchars_decode($vo['n_content']);} ;?>
			            </script>
			        </div>
			        <div id="markdown" class="mt-20"></div>
			    </div>
			    <div class="col-xs-3 col-sm-3"></div>
			</div>

			或
			<div>
                <script id="editor" name="content" type="text/plain" style="width:1200px;">
                    <?php if (!empty($vo['content'])) {echo htmlspecialchars_decode($vo['content']);} ;?>
                </script>
            </div>

            <script>window.UEDITOR_HOME_URL = '__LIB__/ueditor/1.4.3/'</script>
			<script type="text/javascript" charset="utf-8" src="__LIB__/ueditor/1.4.3/ueditor.config.js"></script>
			<script type="text/javascript" charset="utf-8" src="__LIB__/ueditor/1.4.3/ueditor.all.min.js"> </script>
			<script type="text/javascript" charset="utf-8" src="__LIB__/ueditor/1.4.3/lang/zh-cn/zh-cn.js"></script>
			<script>
			    $(function () {
			        var ue = UE.getEditor('editor',{
			            serverUrl:'{:\\think\\Url::build("Ueditor/index")}',
			            enterTag : 'br',
			        });
			    })
			</script>
			
	日志记录：application/admin/tags.php 注销后不记录操作日志(已注销)
			或者 application/common/behavior/Weblog.php  设置$forbid = true;(已设置)

			//方法名record内
			     在
			// 组装数据
	        $log = self::$param;
                 下添加
	        //方法记录限制
	        if ($log[self::ACTION] != 'index') {
	            return true;
	        }


	layer:  父级                     parent.layer
			提示信息                 layer.mag('提示信息',{time: 100000000})
			关闭                     layer.close(index)   index->当前
			关闭所有                 layer.closeAll(type)  
										type->类型('iframe'=>iframe层,'dialog'=>信息框,'page'=>页面层,'loading'=>加载层,'tips'=>tips层)

	insert: 避免重复插入  设置字段索引 索引类型=>unique  索引方法=>BTREE
							      Db::name('aa')->insert($data,true);

	import: 新加导入功能
			\application\admin\traits\controller\Controller.php:404行

	联表模板： 	protected function filter(&$map)
			    {
			        if ($this->request->param("ssid")) {
			            $map['ssid'] = ["like", "%" . $this->request->param("ssid") . "%"];
			        }
			        $map['_table'] = 'tp_sign'; // 强制加表名
			        $map['_field'] = [
			            'tp_sign.id as id',
			            'tp_sign.user_id as user_id',
			            'tp_sign.integral as integral',
			            'tp_sign.create_time as create_time',
			            'tp_wechat_user.ssid as ssid',
			        ]; // 字段
			        $map['_model'] = $this->getModel()
			            ->join('tp_wechat_user', 'tp_wechat_user.id = user_id', 'left'); // 联表
			    }
			    
	页面获取表单提交的值：{$Request.param.r_name}

	操作按钮：	{tp:menu menu='sedit,sdelete,user,access' url=',,user:id=$vo.id,access:id=$vo.id' 					title=',,用户列表,授权' /}

				/**
			     * 用户列表
			     */
			    public function user()
			    {
			        $role_id = $this->request->param('id/d');
			        if ($this->request->isPost()) {
			            // 提交
			            return ajax_return_adv("分配角色成功", '');
			        } else {
			            // 编辑页
			            return $this->view->fetch();
			        }
			    }


	标签优化：原  case 'sdelete':
                    $title = isset($titleArr[$k]) && $titleArr[$k] ? $titleArr[$k] : '删除';
                    list($url, $param) = $this->parseUrl($url);
                    $parseStr .= ' <a title="' . $title . '" href="javascript:;" onclick="del(this,\'{$vo.id}\',\'<?php echo \think\Url::build(\'' . $url . '\', [' . $param . ']); ?>\')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>';
                    break;
             改：case 'sdelete':
                    $title = isset($titleArr[$k]) && $titleArr[$k] ? $titleArr[$k] : '删除';
                    list($url, $param) = $this->parseUrl($url);
                    if (!empty($urls[1])) {
                        list($key, $pk_id) = explode("=", $urls[1]);
                    }else{
                        $pk_id = '$vo.id';
                    }                   
                    $parseStr .= ' <a title="' . $title . '" href="javascript:;" onclick="del(this,\'{'.$pk_id.'}\',\'<?php echo \think\Url::build(\'' . $url . '\', [' . $param . ']); ?>\')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>';
                    break;
            好处：{tp:menu menu='sdelete' url='sdelete:g_id=$vo.g_id' title='删除' /}
            	  如果主键字段不是id而是g_id时，直接可以用该方式修改标签

    增加拼音搜索功能模型：
    	1.在保存文字同时保存文字拼音
    		vendor('pinyin/pinyin');
    		$pinyin = new \Pinyin();
    		/**
		     * 汉字转拼音函数
		     *
		     * @param string $s			汉字字符串
		     * @param bool $quanpin		是否全拼
		     * @param bool $daxie		首字母是否大写
		     * @return string
		     */
    		$a = $pinyin->getpy('我爱你',1,0);    //woaini   默认全拼小写
    		$a = $pinyin->getpy('我爱你',0,1);    //WAN
    		$a = $pinyin->getpy('我爱你',1,1);    //WoAiNi

    样式：超过长度自动省略为...
    	 style="text-overflow: ellipsis;overflow:hidden; white-space:nowrap; width:90%;"



    WxTmp.php:使用微信推送模板，直接引用 use WxTmp;   $wxtmp = new WxTmp();

    重定向跳转：
        记住跳转之前的url: return redirect('News/category')->remember();
        跳转回来        ： return redirect()->restore();


    session过期时间：application\config.php   204行
                    可在模块内单独添加过期时间，不添加默认1800秒

                    thinkphp\library\think\session.php    136行 
                    方法：public static function set($name, $value = '', $prefix = null, $expire = null){}
                    加入过期时间参数(没测试，不知道有没有效果)，可给单独的session设置过期时间，不需要统一更灵活


    获取微信端用户信息：application\common\model\Wechat.php     通过微信端登录获取信息
                      application\common\model\Jssdk.php       获取jssdk权限


    判定字段为空： $map['people'] = ['exp','is null'];
    判定字段不为空： $map['people'] = ['exp','is not null'];


    复选框：<div class="row cl">
	            <label class="form-label col-xs-3 col-sm-3">不参与活动的机构：</label>
	            <div class="formControls col-xs-6 col-sm-6" style="margin-top: 4px;">
	                <?php $agents = model('user')->get_agent(); $y=1;?>
	                {volist name="agents" id="vo2"}
	                    <input type="checkbox" name="agent_ids[]" value="{$vo2.id}" {if condition="in_array($vo2.id,$vo.agent_ids)"}checked{/if}  datatype="*" nullmsg="请选择类别"> 
	                    {$vo2.username ?: $vo2.nickname}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	                    <?php if($y%6 == 0): echo '<br>'; endif; $y++;?>
	                {/volist}
	            </div>
	            <div class="col-xs-3 col-sm-3"></div>
	        </div>

	        在模型层中添加字段修改
	        //避免空值时不保存
	        protected $auto = ['agent_ids'];
	        //查询转换
	        public function getAgentIdsAttr($value)
		    {
		        return explode(',', $value);
		    }
		    //保存转换
		    public function setAgentIdsAttr($value)
		    {
		        return implode(',', $value);
		    }

	连续更新累加：
		Db::name('check_number')->where(['c_id'=>1,'c_month'=>['<',$day_last]])->inc('c_meter_low',$low_pressure)->inc('c_meter_high',$high_pressure)->exp('c_month',$day_last)->update();

		或：	Db::name('check_number')->where(['c_id'=>1,'c_month'=>['<',$day_last]])->update(['c_meter_low'=>['exp',"c_meter_low+$low_pressure"],'c_meter_high'=>['exp',"c_meter_high+$high_pressure"],'c_month'=>$day_last]);


	高亮：<td>{$vo.content|high_light=$Request.param.keyword}</td>


    