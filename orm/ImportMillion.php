<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/8
 * Time: 19:38
 */

namespace autoapi\orm;


class ImportMillion
{
    /**
     * @desc    批量导入百万条数据入库（暂时只有韵达，方法已经写通用,数据最少5000条，最大100W条）
     * @date    2017-10-26 20:45:45
     * @param   [int $start_no 起始号；int $end_no 截止号；string $express_type 类型]
     * @author  1245049149@qq.com
     * @return  [type]
     */
    public function import_million_express_no()
    {

        //基本数据设置
        header('Content-Type:text/html;charset=utf-8');
        ini_set('memory_limit', '128M');

        //设置类型对应数据库中的表名
        $express_to_form = [
            'test1' => 'from1',  //平台1对应的表名
            'test2' => 'from2',  //平台2对应的表名
        ];

        //获取参数
        $start_no = trim($this->input->post('start_no'));
        $end_no = trim($this->input->post('end_no'));
        $express_type = trim($this->input->post('express_type'));

        //判断参数是否存在
        if (!$start_no || !$end_no) {
            echo '<script>alert("录入失败，起始号和截止号不能为空为0");history.back();</script>';
            return;
        }

        //起始单号不能大于等于截止单号，录入数量至少为5000个
        if ($start_no >= $end_no) {
            echo '<script>alert("起始号不能，大于等于截止号!");history.back();</script>';
            return;
        } else {
            if ($end_no - $start_no < 5000) {
                $msg="每次录入号不能小于5000个!";
               goto end;

            }
            if ($end_no - $start_no > 1000000) {
                echo '<script>alert("每次录入号不能大于1000000个!");history.back();</script>';
                return;
            }
        }

        //判断数据类型是否存在
        $table_name = $express_to_form[$express_type];
        if (!$table_name) {
            echo '<script>alert("快递类型有误，无法进行打印!");history.back();</script>';
            return;
        } else {

            //判断初始单号,截止单号是否已经录入
            $sql1 = "select id from {$table_name} where express_no = {$start_no}";
            $res1 = $this->db->query($sql1)->row();
            if ($res1) {
                echo '<script>alert("起始号已存在!");history.back();</script>';
                return;
            }

            $sql2 = "select id from {$table_name} where express_no = {$end_no}";
            $res2 = $this->db->query($sql2)->row();
            if ($res2) {
                echo '<script>alert("截止号已存在!");history.back();</script>';
                return;
            }

        }

        /***上面的一系列判断的废话可以不用看，直接看下面怎么对数据进行逻辑处理***/

        //将起始号和截止号进行区间划分
        $length = $end_no - $start_no + 1;
        $times = floor($length / 5000);
        $temp_data = [];
        for ($i = 0; $i < $times; $i++) {
            $temp_data[$i]['start_no'] = $start_no;          //起始编号
            $temp_data[$i]['end_no'] = $start_no + 4999;   //结束编号
            $start_no += 5000;  //下一轮循环的起始编号
        }

        //检验数组最后一组数据，判断是否需要再添加
        if ($end_no > $temp_data[$times - 1]['end_no']) {
            $temp_data[$times]['start_no'] = $temp_data[$times - 1]['end_no'] + 1;
            $temp_data[$times]['end_no'] = $end_no;
        }

        //进行导入数据库sql语句的拼接
        $add_time = time();
        $add_user = $this->session->userdata['user_name'];
        $tmp_val = "('{$add_time}','$add_user',0,'%s',0),";
        for ($j = 0; $j < count($temp_data); $j++) {

            //循环拼接sql插入语句
            $sql = "insert into {$table_name} (field1,field2,field3,field4,field5) values ";
            for ($i = $temp_data[$j]['start_no']; $i <= $temp_data[$j]['end_no']; $i++) $sql .= sprintf($tmp_val, $i);

            $sql = trim($sql, ',') . ';';
            $bool = $this->db->query($sql);
            //执行插入有误，写进日志异常表from3中
            if (!$bool) {
                // 记录日志
                $log_info = array();
                $log_info['field1'] = time();
                $log_info['field2'] = '类型：' . $express_type . '执行有误，单号' . $temp_data[$j]['start_no'] . '-' . $temp_data[$j]['end_no'] . '执行失败';
                $log_info['field3'] = $this->session->userdata['user_name'];
                $this->db->insert('from3', $log_info);
                //错误日志标志
                $err_log_info = TRUE;
            }
        }

        //数据返回
      $msg=  ($err_log_info)? "部分号执行失败，请联系管理员解决!": "数据执行成功！！！";
       echo  '<script>alert("${msg}");history.back();</script>';
       return;
end:
        echo '<script>alert("${msg}");history.back();</script>';
        return;

    }
}