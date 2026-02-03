////
////  EngineerSignUpSocialVC.swift
////  StarStellar
////
////  Created by Apple on 25/07/19.
////  Copyright © 2019 Apple. All rights reserved.
////
//
//import UIKit
//import GoogleSignIn
//
//class EngineerSignUpSocialVC: BaseTableViewController, GIDSignInDelegate, GIDSignInUIDelegate, UITextFieldDelegate {
//    
//    @IBOutlet weak var txtFieldName: FPTextField!
//    @IBOutlet weak var txtFieldEmail: FPTextField!
//    var strTECode = ""
//    var strFullName = ""
//    var strEmail = ""
//    
//    
//    
//    
//    //MARK: - View Life Cycle
//    
//    override func viewDidLoad() {
//        super.viewDidLoad()
//        self.designView()
//        self.loadData()
//    }
//    
//    //MARK: - Initialization Method
//    
//    func designView() -> Void {
//        
//    }
//    
//    func loadData() -> Void {
//        
//    }
//    
//    //MARK: - Google SignIn Delegate
//    
//    func sign(inWillDispatch signIn: GIDSignIn!, error: Error!) {
//    }
//    
//    func sign(_ signIn: GIDSignIn!,
//              present viewController: UIViewController!) {
//        self.present(viewController, animated: true, completion: nil)
//    }
//    
//    func sign(_ signIn: GIDSignIn!,
//              dismiss viewController: UIViewController!) {
//        self.dismiss(animated: true, completion: nil)
//    }
//    
//    public func sign(_ signIn: GIDSignIn!, didSignInFor user: GIDGoogleUser!,
//                     withError error: Error!) {
//        if (error == nil) {
//            // Perform any operations on signed in user here.
//            //let userId = user.userID                  // For client-side use only!
//            //let idToken = user.authentication.idToken // Safe to send to the server
//            //let givenName = user.profile.givenName
//            //let familyName = user.profile.familyName
//            strFullName = user.profile.name
//            strEmail = user.profile.email
//            
//            performSegue(withIdentifier: "signUpSocialToMobile", sender: self)
//            
//            // ...
//        } else {
//            print("\(error.localizedDescription)")
//        }
//    }
//    
//    
//    
//    //MARK: - IBAction's
//    
//    
//    
//    @IBAction func btnGoogleClicked(_ sender: FPButton) {
//        GIDSignIn.sharedInstance()?.delegate = self
//        GIDSignIn.sharedInstance()?.uiDelegate = self
//        GIDSignIn.sharedInstance()?.signIn()
//    }
//    
//    @IBAction func btnOkClicked(_ sender: FPButton) {
//        
//        if txtFieldName.text?.trimmingCharacters(in: .whitespaces).count == 0 {
//            showToastAlert("Please enter name")
//            return
//        }else if !LogicConstant().validateEmail(emailStr: txtFieldEmail.text!) {
//            showToastAlert("Please enter valid email")
//            return
//        }
//        
//        strFullName = txtFieldName.text!
//        strEmail = txtFieldEmail.text!
//        
//        performSegue(withIdentifier: "signUpSocialToMobile", sender: self)
//    }
//    
//    //MARK: - UITextField Delegate
//    
//    func textFieldShouldReturn(_ textField: UITextField) -> Bool {
//        return textField.resignFirstResponder()
//    }
//    
//    //MARK: - Segue
//    
//    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
//        if segue.identifier == "signUpSocialToMobile" {
//            let esumvc = segue.destination as? EngineerSignUpMobileVC
//            esumvc?.strName = strFullName
//            esumvc?.strEmail = strEmail
//            esumvc?.strTECode = strTECode
//        }
//    }
//    
//    
//}


//
//  EngineerSignUpSocialVC.swift
//  StarStellar
//
//  Created by Apple on 25/07/19.
//  Updated for Google Sign-In v7+
//

import UIKit
import GoogleSignIn
import FirebaseCore

class EngineerSignUpSocialVC: BaseTableViewController, UITextFieldDelegate {
    
    @IBOutlet weak var txtFieldName: FPTextField!
    @IBOutlet weak var txtFieldEmail: FPTextField!
    
    var strTECode = ""
    var strFullName = ""
    var strEmail = ""
    
    // MARK: - View Life Cycle
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.designView()
        self.loadData()
    }
    
    // MARK: - Initialization Methods
    
    func designView() {
        // Customize UI if needed
    }
    
    func loadData() {
        // Load any initial data if needed
    }
    
    // MARK: - IBAction's
    @IBAction func btnGoogleClicked(_ sender: FPButton) {
        // Replace with your actual client ID
        let clientID = "1003435348766-9jvgkqf1phkmfaf59bdssgen4cdktev0.apps.googleusercontent.com"
        
        let config = GIDConfiguration(clientID: clientID)
        
        GIDSignIn.sharedInstance.signIn(withPresenting: self) { result, error in
            if let error = error {
                print("Google Sign-In Error: \(error.localizedDescription)")
                return
            }
            
            guard let googleUser = result?.user, let profile = googleUser.profile else {
                    return
                }
            
            self.strFullName = profile.name
            self.strEmail = profile.email
            
            self.performSegue(withIdentifier: "signUpSocialToMobile", sender: self)
        }
    }
//    @IBAction func btnGoogleClicked(_ sender: FPButton) {
//        // Ensure client ID is set
//        guard let clientID = FirebaseApp.app()?.options.clientID else {
//            print("Google Client ID not set")
//            return
//        }
//        
//        let config = GIDConfiguration(clientID: clientID)
//        
//        GIDSignIn.sharedInstance.signIn(with: config, presenting: self) { [weak self] user, error in
//            if let error = error {
//                print("Google Sign-In Error: \(error.localizedDescription)")
//                return
//            }
//            
//            guard let self = self, let profile = user?.profile else { return }
//            
//            self.strFullName = profile.name
//            self.strEmail = profile.email
//            
//            self.performSegue(withIdentifier: "signUpSocialToMobile", sender: self)
//        }
//    }
    
    @IBAction func btnOkClicked(_ sender: FPButton) {
        guard let name = txtFieldName.text?.trimmingCharacters(in: .whitespaces), !name.isEmpty else {
            showToastAlert("Please enter name")
            return
        }
        
        guard let email = txtFieldEmail.text, LogicConstant().validateEmail(emailStr: email) else {
            showToastAlert("Please enter valid email")
            return
        }
        
        strFullName = name
        strEmail = email
        
        performSegue(withIdentifier: "signUpSocialToMobile", sender: self)
    }
    
    // MARK: - UITextField Delegate
    
    func textFieldShouldReturn(_ textField: UITextField) -> Bool {
        return textField.resignFirstResponder()
    }
    
    // MARK: - Segue
    
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        if segue.identifier == "signUpSocialToMobile",
           let esumvc = segue.destination as? EngineerSignUpMobileVC {
            esumvc.strName = strFullName
            esumvc.strEmail = strEmail
            esumvc.strTECode = strTECode
        }
    }
}
