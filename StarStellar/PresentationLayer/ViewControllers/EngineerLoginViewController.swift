//
//  EngineerLoginViewController.swift
//  StarStellar
//
//  Created by Apple on 18/07/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit

class EngineerLoginViewController: BaseTableViewController, UITextFieldDelegate {
    
    @IBOutlet weak var txtFieldTECode: FPTextField!
    @IBOutlet weak var txtFieldPhone: FPTextField!
    
    //MARK: - View Life Cycle
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.designView()
        self.loadData()
    }
    
    //MARK: - Initialization Method
    
    func designView() -> Void {
        self.setupToolbar()
    }
    
    func loadData() -> Void {
        
    }
    
    //MARK: - IBAction's
    
    @IBAction func btnLoginClicked(_ sender: FPButton) {
        
    
        
//        let strTECode = txtFieldTECode.text?.trimmingCharacters(in: .whitespaces)
//        if strTECode == "" {
//            showToastAlert("Please enter TE Code")
//            return
//        }else if !LogicConstant().validateMobileNumber(txtFieldPhone.text){
//            showToastAlert("Please enter valid mobile number")
//            return
//        }
        performSegue(withIdentifier: "engineerLoginToOTP", sender: self)
        
    }
    
    @IBAction func btnLoginAsTEClicked(_ sender: UIButton) {
        
    }
    
    //MARK: - UITextField Delegate
    
    public func textFieldShouldReturn(_ textField: UITextField) -> Bool{
        return textField.resignFirstResponder()
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
}

