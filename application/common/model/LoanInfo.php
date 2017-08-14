<?php

namespace app\common\model;

use think\Model;
use org\PrivateImage;

//贷款信息 基础信息
class LoanInfo extends Model
{
    public function User()
    {
        return $this->belongsTo('User', 'user_id', 'id');
    }

    public function LoanInfoCredit()
    {
        return $this->hasOne('LoanInfoCredit', 'loan_id', 'id');
    }

    public function LoanInfoHouse()
    {
        return $this->hasOne('LoanInfoHouse', 'loan_id', 'id');
    }

    public function LoanInfoCar()
    {
        return $this->hasOne('LoanInfoCar', 'loan_id', 'id');
    }

    public function LoanInfoBeauty()
    {
        return $this->hasOne('LoanInfoBeauty', 'loan_id', 'id');
    }

    private function _add($data, $extendData, $relation)
    {
        //开启事务
        $this->startTrans();
        try {
            $isUpdate = $this->isUpdate;
            //保存公共信息
            $this->allowField(true)->save($data);
            //保存扩展信息
            if ($isUpdate) {
                $this->$relation->save($extendData);
            } else {
                $this->$relation()->save($extendData);
            }
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
        return true;
    }

    public function loanInfoDetail($where)
    {
        $result = $this->where($where)->find();
        $userId = User::userIdById($result->user_id);

        $take_id_pics_array = explode(',', $result->take_id_pics);
        $id_pics = array();
        foreach ($take_id_pics_array as $pic) {
            $id_pics[] = PrivateImage::getImageUrl($pic, $userId);
        }
        $result->take_id_pics_array = $id_pics;
        if ($result->house_hold_book_pic) {
            $house_hold_book_pic_array = explode(',', $result->house_hold_book_pic);
            $id_pics = array();
            foreach ($house_hold_book_pic_array as $pic) {
                $id_pics[] = PrivateImage::getImageUrl($pic, $userId);
            }
            $result->house_hold_book_pic_array = $id_pics;
        }


        if ($result->marry_book_pic) {
            $result->marry_book_pic_array = array(PrivateImage::getImageUrl($result->marry_book_pic, $userId));
        }
        $result->credit_report_pic_array = array(PrivateImage::getImageUrl($result->credit_report_pic, $userId));
        switch ($result->getData('type')) {
            case '车贷':
                $result->car_brand = $result->LoanInfoCar->car_brand;
                $result->car_mill = $result->LoanInfoCar->car_mill;
                $result->loan_buy_car = $result->LoanInfoCar->loan_buy_car;
                $result->car_full_pic = $result->LoanInfoCar->car_full_pic;
                $result->car_full_pic_array = array(PrivateImage::getImageUrl($result->LoanInfoCar->car_full_pic, $userId));
                $result->car_reg_pic = $result->LoanInfoCar->car_reg_pic;
                $result->car_reg_pic_array = array(PrivateImage::getImageUrl($result->LoanInfoCar->car_reg_pic, $userId));
                $result->driving_licence_pics = $result->LoanInfoCar->driving_licence_pics;
                $result->driving_licence_pics_array = array(PrivateImage::getImageUrl($result->LoanInfoCar->driving_licence_pics, $userId));
                $result->vehicle_licence_pics = $result->LoanInfoCar->vehicle_licence_pics;
                $result->vehicle_licence_pics_array = array(PrivateImage::getImageUrl($result->LoanInfoCar->vehicle_licence_pics, $userId));
                $result->car_invoice_pic = $result->LoanInfoCar->car_invoice_pic;
                $result->car_invoice_pic_array = array(PrivateImage::getImageUrl($result->LoanInfoCar->car_invoice_pic, $userId));
                $result->policy_pic = $result->LoanInfoCar->policy_pic;
                $result->policy_pic_array = array(PrivateImage::getImageUrl($result->LoanInfoCar->policy_pic, $userId));
                break;
            case '房贷':
                $result->loan_buy_house = $result->LoanInfoHouse->loan_buy_house;
                $result->house_both_have = $result->LoanInfoHouse->house_both_have;
                $result->house_may_price = $result->LoanInfoHouse->house_may_price;
                break;
            case '信贷':
                $result->had_house = $result->LoanInfoCredit->had_house;
                $result->had_car = $result->LoanInfoCredit->had_car;
                $result->had_loan = $result->LoanInfoCredit->had_loan;
                $result->job_company_type = $result->LoanInfoCredit->job_company_type;
                $result->monthly = $result->LoanInfoCredit->monthly;
                $result->social_security = $result->LoanInfoCredit->social_security;
                $result->provident_fund = $result->LoanInfoCredit->provident_fund;
                $result->in_come_certificate_pic = $result->LoanInfoCredit->in_come_certificate_pic;
                if ($result->in_come_certificate_pic) {
                    $result->in_come_certificate_pic_array = array(PrivateImage::getImageUrl($result->LoanInfoCredit->in_come_certificate_pic, $userId));
                }
                break;
            case '美丽贷':
                $result->had_house = $result->LoanInfoBeauty->had_house;
                $result->had_car = $result->LoanInfoBeauty->had_car;
                $result->had_loan = $result->LoanInfoBeauty->had_loan;
                $result->take_bank_card_pic = $result->LoanInfoBeauty->take_bank_card_pic;
                $result->take_bank_card_pic_array = array(PrivateImage::getImageUrl($result->LoanInfoBeauty->take_bank_card_pic, $userId));
                $result->father_name = $result->LoanInfoBeauty->father_name;
                $result->father_phone = $result->LoanInfoBeauty->father_phone;
                $result->mother_name = $result->LoanInfoBeauty->mother_name;
                $result->mother_phone = $result->LoanInfoBeauty->mother_phone;
                $result->friend1_name = $result->LoanInfoBeauty->friend1_name;
                $result->friend1_phone = $result->LoanInfoBeauty->friend1_phone;
                $result->friend2_name = $result->LoanInfoBeauty->friend2_name;
                $result->friend2_phone = $result->LoanInfoBeauty->friend2_phone;
                $result->friend3_name = $result->LoanInfoBeauty->friend3_name;
                $result->friend3_phone = $result->LoanInfoBeauty->friend3_phone;
                break;
        }

        return $result;
    }

    /**
     * commitCreditLoan
     * @param $data
     * @return bool|string
     */
    public function commitCreditLoan($data)
    {
        $extendData = array(
            'job_company_type' => $data['job_company_type'],
            'monthly' => $data['monthly'],
            'provident_fund' => $data['provident_fund'],
            'had_house' => $data['had_house'],
            'had_car' => $data['had_car'],
            'had_loan' => $data['had_loan'],
            'social_security' => $data['social_security'],
            'in_come_certificate_pic' => $data['in_come_certificate_pic']
        );
        return $this->_add($data, $extendData, 'LoanInfoCredit');
    }

    /**
     * commitHouseLoan
     * @param $data
     * @return bool|string
     */
    public function commitHouseLoan($data)
    {
        $extendData = array(
            'loan_buy_house' => $data['loan_buy_house'],
            'house_both_have' => $data['house_both_have'],
            'house_may_price' => $data['house_may_price']
        );
        return $this->_add($data, $extendData, 'LoanInfoHouse');
    }

    /**
     * commitCarLoan
     * @param $data
     * @return bool|string
     */
    public function commitCarLoan($data)
    {
        $extendData = array(
            'car_brand' => $data['car_brand'],
            'car_mill' => $data['car_mill'],
            'loan_buy_car' => $data['loan_buy_car'],
            'car_full_pic' => $data['car_full_pic'],
            'car_reg_pic' => $data['car_reg_pic'],
            'driving_licence_pics' => $data['driving_licence_pics'],
            'vehicle_licence_pics' => $data['vehicle_licence_pics'],
            'car_invoice_pic' => $data['car_invoice_pic'],
            'policy_pic' => $data['policy_pic'],
        );
        return $this->_add($data, $extendData, 'LoanInfoCar');
    }

    /**
     * commitBeautyLoan
     * @param $data
     * @return bool|string
     */
    public function commitBeautyLoan($data)
    {
        $extendData = array(
            'had_house' => $data['had_house'],
            'had_car' => $data['had_car'],
            'had_loan' => $data['had_loan'],
            'take_bank_card_pic' => $data['take_bank_card_pic'],
            'father_name' => $data['father_name'],
            'father_phone' => $data['father_phone'],
            'mother_name' => $data['mother_name'],
            'mother_phone' => $data['mother_phone'],
            'friend1_name' => $data['friend1_name'],
            'friend1_phone' => $data['friend1_phone'],
            'friend2_name' => $data['friend2_name'],
            'friend2_phone' => $data['friend2_phone'],
            'friend3_name' => $data['friend3_name'],
            'friend3_phone' => $data['friend3_phone']
        );
        return $this->_add($data, $extendData, 'LoanInfoBeauty');
    }

    /**
     * 信息删除
     * @return bool|string
     */
    public function del()
    {
        switch ($this->type) {
            case '房贷':
                $relation = 'LoanInfoHouse';
                break;
            case '车贷':
                $relation = 'LoanInfoCar';
                break;
            case '信贷':
                $relation = 'LoanInfoCredit';
                break;
            case '美丽贷':
                $relation = 'LoanInfoBeauty';
                break;
        }
        //开启事务
        $this->startTrans();
        try {
            if (isset($relation) && $relation) {
                $this->$relation()->delete();
            }
            $this->delete();
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
        return true;
    }
}
