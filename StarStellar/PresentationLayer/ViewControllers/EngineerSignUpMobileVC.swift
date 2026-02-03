//
//  EngineerSignUpMobileVC.swift
//  StarStellar
//
//  Created by Apple on 25/07/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit
import SVProgressHUD
import SwiftyJSON

class EngineerSignUpMobileVC: BaseTableViewController {
    
    @IBOutlet weak var txtFieldMobile: FPTextField!
    var strName = ""
    var strEmail = ""
    var strTECode = ""
    
    
    //MARK: - View Life Cycle
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.designView()
        self.loadData()
    }
    
    //MARK: - Initialization Method
    
    func designView() -> Void {
        setupToolbar()
    }
    
    func loadData() -> Void {
        
    }
    
    //MARK: - IBAction's
    
    @IBAction func btnLoginClicked(_ sender: FPButton) {
       
        
        if !LogicConstant().validateMobileNumber(txtFieldMobile.text) {
            showToastAlert("Please enter valid mobile number")
            return
        }
        
        if isServerReachable() {
            //te_code,mobile,e_name,e_email
            var dict : [String : Any] = [:]
            dict["te_code"] = strTECode
            dict["mobile"] = txtFieldMobile.text
            dict["e_name"] = strName
            dict["e_email"] = strEmail
            
            SVProgressHUD.show()
            SSParserLayer.callGenerateOTPForEngineerLogin(dict) { (strStatus, strMessage, dictResponse) in
                SVProgressHUD.dismiss()
                if strStatus == "YES" {
                  self.performSegue(withIdentifier: "signUpMobileToOTP", sender: self)
                }else{
                    self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                }
            }
            
        }else{
            showToastAlert(StringConstant.kNoInternet)
        }
        
    }
    
    //MARK: - Segue
    
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        if segue.identifier == "signUpMobileToOTP" {
            let otpvc = segue.destination as? OTPViewController
            otpvc?.strTECode = strTECode
            otpvc?.strMobileNumber = txtFieldMobile.text!
        }
    }
    
    //MARK: - Helper Method
    
    func setupToolbar() -> Void {
        let numberToolbar = UIToolbar(frame:CGRect(x: 0, y: 0, width: UIScreen.main.bounds.width, height: 50))
        numberToolbar.barStyle = .default
        numberToolbar.items = [
            UIBarButtonItem(barButtonSystemItem: .flexibleSpace, target: nil, action: nil),
            UIBarButtonItem(title: "Done", style: .plain, target: self, action: #selector(doneWithNumberPad))]
        numberToolbar.sizeToFit()
        txtFieldMobile.inputAccessoryView = numberToolbar
    }
    
    @objc func doneWithNumberPad() {
        self.view.endEditing(true)
    }
    
    
}
