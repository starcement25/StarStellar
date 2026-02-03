//
//  GiftDetailsViewController.swift
//  StarStellar
//
//  Created by Apple on 03/08/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit
import SwiftyJSON
import Alamofire
import SVProgressHUD
import SDWebImage

protocol StellarPointsDelegate {
    func updateStellarPoints(intStellerPoints : Int)
}

class GiftDetailsViewController: BaseTableViewController {
    
    @IBOutlet weak var viewBase: UIView!
    @IBOutlet weak var imgViewGift: UIImageView!
    @IBOutlet weak var lblProductName: UILabel!
    @IBOutlet weak var lblPoint: UILabel!
    @IBOutlet weak var lblMyPoints: UILabel!
    @IBOutlet weak var lblProductPoints: UILabel!
    @IBOutlet weak var lblBalance: UILabel! 
    @IBOutlet weak var btnConfirm: FPButton!
    
    @IBOutlet weak var txtViewAddress: FPTextView!
    @IBOutlet weak var txtFieldCity: UITextField!
    @IBOutlet weak var txtFieldPin: UITextField!
    @IBOutlet weak var txtFieldState: UITextField!
    @IBOutlet weak var btnSetAsDefault: UIButton!
    @IBOutlet weak var btnTermAndConditions: UIButton!
    
    var dictGift : JSON = []
    var intMyStellarPoints = 0
    
    var delegate : StellarPointsDelegate?
    
    //MARK: - View Life Cycle
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.designView()
        self.loadData()
    }
    
    override func viewDidLayoutSubviews() {
        super.viewDidLayoutSubviews()
        
        viewBase.frame = CGRect(x: 0, y: 0, width: self.view.bounds.size.width, height: btnConfirm.frame.origin.y + btnConfirm.frame.size.height + 10)
        tableView.contentSize = CGSize(width: self.view.bounds.size.width, height: viewBase.frame.size.height)
        
    }
    
    //MARK: - Initialization Method
    
    func designView() -> Void {
        setupToolbar()
    }
    
    func loadData() -> Void {
        print(dictGift)
        print("My Stellar points:\(intMyStellarPoints)")
        
        lblProductName.text = dictGift["gift_title"].stringValue
        lblPoint.text = "Point - \(dictGift["point_require"].stringValue)"
        lblMyPoints.text = "\(intMyStellarPoints)"
        lblProductPoints.text = dictGift["point_require"].stringValue
        //lblBalance.text = "\(Int(strMyStellarPoints)! - dictGift["point_require"].intValue)"
        lblBalance.text = "\(intMyStellarPoints - dictGift["point_require"].intValue)"
        imgViewGift.sd_setImage(with: URL(string: dictGift["gift_image_url"].stringValue), placeholderImage: UIImage(named: "image_placeholder"))
        
//        request(dictGift["gift_image_url"].stringValue, method: .get)
//            .validate()
//            .responseData(completionHandler: { (responseData) in
//                self.imgViewGift.image = UIImage(data: responseData.data!)
//            })
        
        txtViewAddress.text = Defaults.engineerAddress()
        txtFieldCity.text   = Defaults.engineerCity()
        txtFieldPin.text    = Defaults.engineerPincode()
        txtFieldState.text  = Defaults.engineerState()        
        
    }
    
    //MARK: - IBAction's
    @IBAction func btnBackClicked(_ sender: AnyObject?) {
        
        let alert = UIAlertController(title: nil, message: "Do you want to cancel the Redemption?", preferredStyle: UIAlertController.Style.alert)
        alert.addAction(UIAlertAction(title: "NO", style: UIAlertAction.Style.cancel, handler:nil))
        alert.addAction(UIAlertAction(title: "YES", style: UIAlertAction.Style.default, handler: { (action) in
            self.navigationController?.popViewController(animated: true)
//            for controller in self.navigationController!.viewControllers as Array {
//                if controller.isKind(of: EngineerDashboardViewController.self) {
//                    self.navigationController!.popToViewController(controller, animated: true)
//                    break
//                }
//            }
        }))
        present(alert, animated: true, completion: nil)    
        
    }
    
    @IBAction func btnConfirmClicked(_ sender: FPButton) {
        
        if txtViewAddress.text?.trimmingCharacters(in: .whitespaces).count == 0 {
            showToastAlert("Please enter address")
            return
        }else if txtFieldCity.text?.trimmingCharacters(in: .whitespaces).count == 0 {
            showToastAlert("Please enter city")
            return
        }else if txtFieldPin.text?.trimmingCharacters(in: .whitespaces).count != 6 {
            showToastAlert("Please enter 6 digit pincode")
            return
        }else if txtFieldState.text?.trimmingCharacters(in: .whitespaces).count == 0 {
            showToastAlert("Please enter state")
            return
        }else if !btnTermAndConditions.isSelected {
            showToastAlert("Please accept Terms and Conditions")
            return
        }
        
        if isServerReachable() {
            
            var dict : [String : Any] = [:]
            
            dict["gift_id"] = dictGift["gift_id"].stringValue
            dict["the_engineer_id"] = Defaults.engineerId()
            dict["set_as_default_profile_address"] = btnSetAsDefault.isSelected ? "YES" : "NO"
            dict["e_address"] = txtViewAddress.text
            dict["e_city"] = txtFieldCity.text
            dict["e_pin"] = txtFieldPin.text
            dict["e_state"] = txtFieldState.text
            
            SVProgressHUD.show()
            SSParserLayer.callMakeGiftOrder(dict) { (strStatus, strMessage, dictResponse) in
               SVProgressHUD.dismiss()
                
                if strStatus == "YES" {
                    
                    let json = JSON(dictResponse!)
                    print("-->",json["the_point"].stringValue)
                    
                    if self.delegate != nil {
                        self.delegate?.updateStellarPoints(intStellerPoints: json["the_point"].intValue)
                    }
                    
                    if self.btnSetAsDefault.isSelected {
                        UserDefaults.standard.set(self.txtViewAddress.text, forKey: "e_address")
                        UserDefaults.standard.set(self.txtFieldPin.text,    forKey: "e_pin")
                        UserDefaults.standard.set(self.txtFieldState.text,  forKey: "e_state")
                        UserDefaults.standard.set(self.txtFieldCity.text,   forKey: "e_city_town")
                        UserDefaults.standard.synchronize()
                    }
                    
                    //self.navigationController?.popViewController(animated: true)
                    
                    for controller in self.navigationController!.viewControllers as Array {
                        if controller.isKind(of: EngineerDashboardViewController.self) {
                            self.navigationController!.popToViewController(controller, animated: true)
                            break
                        }
                    }
                    self.showToastAlert(strMessage ?? "Order successfully placed.")
                    
                    
                }else{
                    self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                }
            }
            
        }else {
            showToastAlert(StringConstant.kNoInternet)
        }
        
        
    
    }
    
    @IBAction func btnSetAsDefaultClicked(_ sender: UIButton) {
        sender.isSelected = !sender.isSelected
    }
    
    @IBAction func btnTermAndConditionsClicked(_ sender: UIButton) {
        sender.isSelected = !sender.isSelected
    }
    //MARK: - Helper Method
    
    func setupToolbar() -> Void {
        
        let numberToolbar = UIToolbar(frame:CGRect(x: 0, y: 0, width: UIScreen.main.bounds.width, height: 50))
        numberToolbar.barStyle = .default
        numberToolbar.items = [
            UIBarButtonItem(barButtonSystemItem: .flexibleSpace, target: nil, action: nil),
            UIBarButtonItem(title: "Done", style: .plain, target: self, action: #selector(doneWithNumberPad))]
        numberToolbar.sizeToFit()
        
        txtViewAddress.inputAccessoryView = numberToolbar
        txtFieldPin.inputAccessoryView = numberToolbar
    }
    
    @objc func doneWithNumberPad() {
        self.view.endEditing(true)
    }
    
}

extension GiftDetailsViewController : UITextFieldDelegate {
    func textFieldShouldReturn(_ textField: UITextField) -> Bool {
        return textField.resignFirstResponder()
    }
}
