//
//  OTPViewController.swift
//  StarStellar
//
//  Created by Apple on 19/07/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit
import SVProgressHUD
import SwiftyJSON

class OTPViewController: BaseTableViewController, UITextFieldDelegate {
    
    @IBOutlet weak var txtFieldFirst: UITextField!
    @IBOutlet weak var txtFieldSecond: UITextField!
    @IBOutlet weak var txtFieldThird: UITextField!
    @IBOutlet weak var txtFieldFourth: UITextField!
    var strTECode = ""
    var strMobileNumber = ""
    
    
    
    //MARK: - View Life Cycle
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.designView()
        self.loadData()
    }
    
    //MARK: - Initialization Method
    
    func designView() -> Void {
        
        txtFieldFirst.addTarget(self, action: #selector(self.textFieldDidChange(_:)), for: UIControl.Event.editingChanged)
        txtFieldSecond.addTarget(self, action: #selector(self.textFieldDidChange(_:)), for: UIControl.Event.editingChanged)
        txtFieldThird.addTarget(self, action: #selector(self.textFieldDidChange(_:)), for: UIControl.Event.editingChanged)
        txtFieldFourth.addTarget(self, action: #selector(self.textFieldDidChange(_:)), for: UIControl.Event.editingChanged)
        
    }
    
    func loadData() -> Void {
        
    }
    
    //MARK: - UITextField Delegate
    
    func textFieldDidBeginEditing(_ textField: UITextField){
        textField.text = ""        
    }
    
    @objc func textFieldDidChange(_ textField: UITextField) {
        
        print("-->>",textField.text!)
        
        if (textField.text?.utf16.count)! >= 1{
            switch textField.tag {
            case 101:
                txtFieldSecond.becomeFirstResponder()
            case 102:
                txtFieldThird.becomeFirstResponder()
            case 103:
                txtFieldFourth.becomeFirstResponder()
            case 104:
                txtFieldFourth.resignFirstResponder()
                validateOTP()
            default:
                break
            }
        }
    }
    
    //MARK: - IBAction's
    
    @IBAction func btnResendClicked(_ sender: UIButton) {
        
    }
    
    //MARK: - Helper Method
    
    func validateOTP() -> Void {
        var strOTP = String(format: "%@%@%@%@", txtFieldFirst.text!,txtFieldSecond.text!,txtFieldThird.text!,txtFieldFourth.text!)
        strOTP = strOTP.trimmingCharacters(in: .whitespaces)
        print("OTP:",strOTP)
        if strOTP.count == 4 {
            
            if strTECode.count > 0{
                
                if isServerReachable(){
                    //te_code,mobile,otp,device_id,registration_id,device_type
                    var dict: [String : Any] = [:]
                    dict["te_code"]          = strTECode
                    dict["mobile"]           = strMobileNumber
                    dict["otp"]              = strOTP
                    dict["device_id"]        = StringConstant.Device.Id
                    dict["registration_id"]  = Defaults.deviceToken()
                    dict["device_type"]      = StringConstant.Device.DeviceType
                    
                    SVProgressHUD.show()
                    SSParserLayer.callEngineerLogin(dict, handler: { strStatus, strMessage, dictResponse in
                        SVProgressHUD.dismiss()
                        
                        if strStatus == "YES"{
                            let json = JSON(dictResponse!)
                            
                            UserDefaults.standard.set(true, forKey: "logged_in")
                            
                            UserDefaults.standard.set("ENGINEER",                           forKey: "user_type")
                            UserDefaults.standard.set(json["the_engineer_id"].stringValue,  forKey: "the_engineer_id")
                            UserDefaults.standard.set(json["e_name"].stringValue,           forKey: "e_name")
                            UserDefaults.standard.set(json["e_mobile"].stringValue,         forKey: "mobile_number")
                            UserDefaults.standard.set(json["te_code"].stringValue,          forKey: "te_code")
                            UserDefaults.standard.set(json["e_email"].stringValue,          forKey: "e_email")
                            UserDefaults.standard.set(json["e_dob"].stringValue,            forKey: "e_dob")
                            UserDefaults.standard.set(json["e_dom"].stringValue,            forKey: "e_dom")
                            UserDefaults.standard.set(json["e_address"].stringValue,        forKey: "e_address")
                            UserDefaults.standard.set(json["e_pin"].stringValue,            forKey: "e_pin")
                            UserDefaults.standard.set(json["e_state"].stringValue,          forKey: "e_state")
                            UserDefaults.standard.set(json["e_city_town"].stringValue,      forKey: "e_city_town")
                            UserDefaults.standard.set(json["e_profile_image"].stringValue,  forKey: "e_profile_image")
                            UserDefaults.standard.set("ENGINEER",                           forKey: "logged_in_type")
                            UserDefaults.standard.synchronize()
                            self.performSegue(withIdentifier: "otpToEngineerDashboard", sender: self)
                            self.showToastAlert(strMessage ?? "Thank you for signing up with Star Stellar.")
                            
                            
                        }else{
                            self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                        }
                        
                    })
                    
                }else{
                    showToastAlert(StringConstant.kNoInternet)
                }
                
            }else{
                if isServerReachable(){
                    //mobile,user_type,otp,device_id,registration_id,device_type
                    var dict: [String : Any] = [:]
                    dict["mobile"]          = Defaults.mobileNumber()
                    dict["user_type"]       = Defaults.userType()
                    dict["otp"]             = strOTP
                    dict["device_id"]       = StringConstant.Device.Id
                    dict["registration_id"] = Defaults.deviceToken()
                    dict["device_type"]     = StringConstant.Device.DeviceType
                    
                    SVProgressHUD.show()
                    SSParserLayer.callLoginWithOTPForEngineerAndTE(dict, handler: { strStatus, strMessage, dictResponse in
                        SVProgressHUD.dismiss()
                        
                        if strStatus == "YES"{
                            let json = JSON(dictResponse!)
                            
                            UserDefaults.standard.set(true, forKey: "logged_in")
                            
                            if json["user_type"].stringValue == "ENGINEER" {
                                
                                UserDefaults.standard.set(json["user_type"].stringValue,        forKey: "user_type")
                                UserDefaults.standard.set(json["the_engineer_id"].stringValue,  forKey: "the_engineer_id")
                                UserDefaults.standard.set(json["e_name"].stringValue,           forKey: "e_name")
                                UserDefaults.standard.set(json["e_mobile"].stringValue,         forKey: "mobile_number")
                                UserDefaults.standard.set(json["te_code"].stringValue,          forKey: "te_code")
                                UserDefaults.standard.set(json["e_email"].stringValue,          forKey: "e_email")
                                UserDefaults.standard.set(json["e_dob"].stringValue,            forKey: "e_dob")
                                UserDefaults.standard.set(json["e_dom"].stringValue,            forKey: "e_dom")
                                UserDefaults.standard.set(json["e_address"].stringValue,        forKey: "e_address")
                                UserDefaults.standard.set(json["e_pin"].stringValue,            forKey: "e_pin")
                                UserDefaults.standard.set(json["e_state"].stringValue,          forKey: "e_state")
                                UserDefaults.standard.set(json["e_city_town"].stringValue,      forKey: "e_city_town")
                                UserDefaults.standard.set(json["e_profile_image"].stringValue,  forKey: "e_profile_image")
                                UserDefaults.standard.set(json["user_type"].stringValue,        forKey: "logged_in_type")
                                UserDefaults.standard.synchronize()
                                
                                self.performSegue(withIdentifier: "otpToEngineerDashboard", sender: self)
                                
                            }else{
                                
                                UserDefaults.standard.set(json["the_te_id"].stringValue,        forKey: "the_te_id")
                                UserDefaults.standard.set(json["the_te_email"].stringValue,     forKey: "the_te_email")
                                UserDefaults.standard.set(json["user_type"].stringValue,        forKey: "user_type")
                                UserDefaults.standard.set(json["the_te_mobile_no"].stringValue, forKey: "the_te_mobile_no")
                                UserDefaults.standard.set(json["the_te_code"].stringValue,      forKey: "te_code")
                                UserDefaults.standard.set(json["the_te_name"].stringValue,      forKey: "the_te_name")
                                UserDefaults.standard.set(json["te_profile_image"].stringValue, forKey: "te_profile_image")
                                UserDefaults.standard.set(json["user_type"].stringValue,        forKey: "logged_in_type")
                                UserDefaults.standard.synchronize()
                                
                                self.performSegue(withIdentifier: "otpToTEDashboard", sender: self)
                            }
                            
                        }else{
                            self.showToastAlert(strMessage ?? "")
                        }
                        
                    })
                    
                }else{
                    showToastAlert(StringConstant.kNoInternet)
                }
            }        
            
        }else{
            showToastAlert("Please enter valid OTP")
        }     
        
    }
    
}


