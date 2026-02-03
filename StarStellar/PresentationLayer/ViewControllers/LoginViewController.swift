//
//  LoginViewController.swift
//  StarStellar
//
//  Created by Apple on 24/07/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit
import SVProgressHUD
import SwiftyJSON


class LoginViewController: BaseTableViewController, UITextFieldDelegate {
    
    @IBOutlet weak var txtFieldPhone: FPTextField!
    
    //MARK: - View Life Cycle
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.designView()
        self.loadData()
    }
    
    override func viewWillAppear(_ animated: Bool) {
        navigationController?.setNavigationBarHidden(true, animated: true)        
    }
    
    //MARK: - Initialization Method
    
    func designView() -> Void {
        
        NotificationCenter.default.addObserver(self, selector: #selector(keyboardWillShow), name: UIResponder.keyboardWillShowNotification, object: nil)
        NotificationCenter.default.addObserver(self, selector: #selector(keyboardWillHide), name: UIResponder.keyboardWillHideNotification, object: nil)
        
        setupToolbar()
    }
    
    func loadData() -> Void {
        
    }
    
    //MARK: - UITextField Delegate
    
    func textField(_ textField: UITextField, shouldChangeCharactersIn range: NSRange, replacementString string: String) -> Bool {
        let maxLength = 10
        let currentString: NSString = textField.text! as NSString
        let newString: NSString =
            currentString.replacingCharacters(in: range, with: string) as NSString
        return newString.length <= maxLength
    }
    
    //MARK: - IBAction's
    
    @IBAction func btnLoginClicked(_ sender: FPButton) {
        
        if !LogicConstant().validateMobileNumber(txtFieldPhone.text){
            showToastAlert("Please enter valid mobile number")
            return
        }
        
        if isServerReachable(){
            
            var dict: [String : Any] = [:]
            dict["mobile"] = txtFieldPhone.text
            
            SVProgressHUD.show()
            SSParserLayer.callGenerateOTPForEngineerAndTELogin(dict, handler: { strStatus, strMessage, dictResponse in
                SVProgressHUD.dismiss()
                if (strStatus == "YES") {
                    
                    let json = JSON(dictResponse!)
                    print("-->>UserType :",json["user_type"].stringValue)
                    UserDefaults.standard.set(json["user_type"].stringValue, forKey: "user_type")
                    UserDefaults.standard.set(self.txtFieldPhone.text, forKey: "mobile_number")
                    UserDefaults.standard.synchronize()                    
                    self.performSegue(withIdentifier: "loginToOTP", sender: self)
                    
                }else{
                    self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                }
                
            })
            
        }else{
            showToastAlert(StringConstant.kNoInternet)
        }
    }
    
    @IBAction func btnSignUpAsNewEngineerClicked(_ sender: UIButton) {
        print("Sign up as new engineer")
        performSegue(withIdentifier: "loginToEngineerSignUpTECode", sender: self)
    }
    
    @IBAction func btnTermAndConditionClicked(_ sender: UIButton) {
        print("Term and conditions")
        performSegue(withIdentifier: "loginToTermsAndCondition", sender: self)
    }
    
    //MARK: - Segue
    
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        if segue.identifier == "loginToTermsAndCondition" {
            let wvc = segue.destination as! WebViewController
            wvc.strWeblink = StringConstant.Url.TermsAndConditionsURL
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
        txtFieldPhone.inputAccessoryView = numberToolbar
        
    }
    
    @objc func doneWithNumberPad() {
        self.view.endEditing(true)
    }
    
    @objc func keyboardWillShow(notification: NSNotification) {
        self.view.frame.origin.y -= 25
    }
    
    @objc func keyboardWillHide(notification: NSNotification) {
        if self.view.frame.origin.y != 0 {
            self.view.frame.origin.y = 0
        }
    }
    
    
}
