//
//  EngineerSignUpTECodeVC.swift
//  StarStellar
//
//  Created by Apple on 25/07/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit
import SVProgressHUD
import SwiftyJSON

class EngineerSignUpTECodeVC: BaseTableViewController, UITextFieldDelegate {
    
    @IBOutlet weak var txtFieldTECode: FPTextField!
    //MARK: - View Life Cycle
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.designView()
        self.loadData()
    }
    
    //MARK: - Initialization Method
    
    func designView() -> Void {
        
    }
    
    func loadData() -> Void {
        
    }
    
    //MARK: - IBAction's
    
    @IBAction func btnEnterClicked(_ sender: FPButton) {
        
        if txtFieldTECode.text?.trimmingCharacters(in: .whitespaces).count == 0 {
            showToastAlert("Please enter TE Code")
            return
        }
        
        if isServerReachable() {
            
            var dict : [String : Any] = [:]
            dict["te_code"] = txtFieldTECode.text
            
            SVProgressHUD.show()
            SSParserLayer.callCheckTEExistByTECode(dict) { (strStatus, strMessage, dictResponse) in
                SVProgressHUD.dismiss()
                if strStatus == "YES" {
                    self.performSegue(withIdentifier: "signUpTEToSocial", sender: self)
                }else{
                    self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                }
            }
            
        }else{
            showToastAlert(StringConstant.kNoInternet)
        }
        
        
    }
    
    @IBAction func btnLoginAsTEClicked(_ sender: UIButton) {
        navigationController?.popViewController(animated: true)
    }
    
    //MARK: - UITextField Delegate
    
    func textFieldShouldReturn(_ textField: UITextField) -> Bool {
        return textField.resignFirstResponder()
    }
    
    //MARK: - Segue
    
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        if segue.identifier == "signUpTEToSocial" {
            let esusvc = segue.destination as? EngineerSignUpSocialVC
            esusvc?.strTECode = txtFieldTECode.text!
        }
    }
    
    
}
