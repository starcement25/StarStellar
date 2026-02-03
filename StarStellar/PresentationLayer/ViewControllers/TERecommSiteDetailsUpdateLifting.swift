//
//  TERecommSiteDetailsUpdateLifting.swift
//  StarStellar
//
//  Created by Sanjeet Kumar on 21/09/22.
//  Copyright © 2022 Apple. All rights reserved.
//

import UIKit
import SwiftyJSON
import SVProgressHUD
import SDWebImage
import Alamofire

class TERecommSiteDetailsUpdateLifting: BaseViewController {
    
    var dictSiteDetails : JSON = [] // dictionary declaration
    var strEngineerId = ""
    
    
    @IBOutlet weak var scrollViewFP: UIScrollView!
    @IBOutlet weak var imgViewSite: UIImageView!
    @IBOutlet weak var btnSiteName: UIButton!
    
    @IBOutlet weak var btnSiteAddress: UIButton!
    @IBOutlet weak var btnSiteMT: UIButton!
    @IBOutlet weak var btnSiteContactPerson: UIButton!
    @IBOutlet weak var btnCategory: UIButton!
    @IBOutlet weak var btnMobile: UIButton!
    
    @IBOutlet weak var txtFieldProduct: UITextField!
    @IBOutlet weak var txtFieldActualConsumption: UITextField!
    @IBOutlet weak var btnDealer: UIButton!
    @IBOutlet weak var btnSubdealer: UIButton!
    @IBOutlet weak var txtFieldName: UITextField!
    @IBOutlet weak var txtFieldArea: UITextField!
    @IBOutlet weak var txtFieldContactNumber: UITextField!
    
    @IBOutlet weak var btnRecommendedBy: UIButton!
    @IBOutlet weak var btnContactNo: UIButton!
    @IBOutlet weak var btnEmail: UIButton!
    
    @IBOutlet weak var txtViewComments: UITextView!
    
    var imagePicker = UIImagePickerController()
    
    var arrExpectedProduct = [JSON]()
    var pickerExpectedProduct: UIPickerView? = nil
    var strDealer = "Dealer"
    var imgSite = UIImage()
    
    //MARK: - View Life Cycle
    
    override func viewDidLoad() {
        super.viewDidLoad()
        designView()
        loadData()
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        NotificationCenter.default.addObserver(self, selector: #selector(keyboardWillShow), name:UIResponder.keyboardWillShowNotification, object: nil)
        NotificationCenter.default.addObserver(self, selector: #selector(keyboardWillHide), name:UIResponder.keyboardWillHideNotification, object: nil)
    }
    
    override func viewWillDisappear(_ animated: Bool) {
        super.viewWillDisappear(animated)
        NotificationCenter.default.removeObserver(self)
    }
    
    //MARK: - Initialization Method
    
    func designView() -> Void {
        
        imagePicker.delegate = self
        
        pickerExpectedProduct = UIPickerView()
        pickerExpectedProduct?.backgroundColor = UIColor.white
        pickerExpectedProduct?.showsSelectionIndicator = true
        pickerExpectedProduct?.delegate = self
        pickerExpectedProduct?.dataSource = self
        txtFieldProduct.inputView = pickerExpectedProduct
        
        let doneToolbar = UIToolbar()
        doneToolbar.barStyle = .default
        doneToolbar.items = [
            UIBarButtonItem(barButtonSystemItem: .flexibleSpace, target: nil, action: nil),
            UIBarButtonItem(title: "Done", style: .plain, target: self, action: #selector(resignKeyboard))]
        doneToolbar.sizeToFit()
        txtFieldProduct.inputAccessoryView = doneToolbar
        txtFieldActualConsumption.inputAccessoryView = doneToolbar
        txtFieldContactNumber.inputAccessoryView = doneToolbar
        txtViewComments.inputAccessoryView = doneToolbar
        
    }
    
    func loadData() -> Void {
        print(dictSiteDetails)
        
        getExpectedProduct()
        
        imgViewSite.sd_setImage(with: URL(string: dictSiteDetails["r_recomended_site_image_url"].stringValue ), placeholderImage: UIImage(named: "image_placeholder"))
        btnSiteName.setTitle(dictSiteDetails["r_site_name"].stringValue, for: .normal)
        btnSiteAddress.setTitle(dictSiteDetails["r_address"].stringValue, for: .normal)
        btnSiteMT.setTitle(dictSiteDetails["r_site_potential_in_mt"].stringValue, for: .normal)
        btnSiteContactPerson.setTitle(dictSiteDetails["r_contact_person_name"].stringValue, for: .normal)
        btnCategory.setTitle(dictSiteDetails["r_contact_person_category_name"].stringValue, for: .normal)
        btnMobile.setTitle(dictSiteDetails["r_mobile_no"].stringValue, for: .normal)
        
        btnRecommendedBy.setTitle(dictSiteDetails["r_recomended_by"].stringValue, for: .normal)
        btnContactNo.setTitle(dictSiteDetails["r_contact_no"].stringValue, for: .normal)
        btnEmail.setTitle(dictSiteDetails["r_email"].stringValue, for: .normal)
    }
    
    //MARK: - IBAction's  
    
    @IBAction func btnBackClicked(_ sender: UIBarButtonItem) {
        navigationController?.popViewController(animated: true)
    }
    
    @IBAction func btnRadioDealerType(_ sender: UIButton) {
        btnDealer.isSelected = false
        btnSubdealer.isSelected = false
        
        strDealer = sender.titleLabel!.text!
        sender.isSelected = true
    }
    
    @IBAction func btnConfirmClicked(_ sender: UIButton) {
        if txtFieldProduct.text?.trimmingCharacters(in: .whitespaces).count == 0 {
            showToastAlert("Please select expected product")
            return
        }else if txtFieldActualConsumption.text?.trimmingCharacters(in: .whitespaces).count == 0 {
            showToastAlert("Please enter expected consumption")
            return
        }else if txtFieldName.text?.trimmingCharacters(in: .whitespaces).count == 0 {
            showToastAlert("Please enter name")
            return
        }else if txtFieldArea.text?.trimmingCharacters(in: .whitespaces).count == 0 {
            showToastAlert("Please enter area")
            return
        }else if !LogicConstant().validateMobileNumber(txtFieldContactNumber.text) {
            showToastAlert("Please enter valid mobile number")
            return
        }
        
        //openCamera()
        openGallary()
    }
    
    //MARK: - Web Service
    
    func getExpectedProduct() -> Void {
        
        if isServerReachable(){
            
            SVProgressHUD.show()
            SSParserLayer.callExpectedProduct(nil, handler: { strStatus, strMessage, dictResponse in
                SVProgressHUD.dismiss()
                if (strStatus == "YES") {
                    
                    let json = JSON(dictResponse!)
                    self.arrExpectedProduct = json["product_data"].arrayValue
                    print(self.arrExpectedProduct)
                    self.pickerExpectedProduct?.reloadAllComponents()
                }else{
                    self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                }
            })
            
        }else{
            showToastAlert(StringConstant.kNoInternet)
        }
    }
    
    func wsAddSiteRecommendationForTE() -> Void {
        
        var dict: [String : String] = [:]
        dict["te_code"]   = Defaults.teCode()
        dict["the_engineer_id"] = strEngineerId
        dict["existing_id"] = dictSiteDetails["r_site_id"].stringValue
        dict["site_name"]  = dictSiteDetails["r_site_name"].stringValue
        dict["contact_person_name"]  = dictSiteDetails["r_contact_person_name"].stringValue
        dict["mobile_no"]  = dictSiteDetails["r_mobile_no"].stringValue
        dict["address"]  = dictSiteDetails["r_address"].stringValue
        dict["site_potential_in_mt"]  = dictSiteDetails["r_site_potential_in_mt"].stringValue
        dict["contact_person_category_name"]  = dictSiteDetails["r_contact_person_category_name"].stringValue
        
        dict["actual_product_id"]  = txtFieldProduct.accessibilityValue
        dict["actual_consumption"]  = txtFieldActualConsumption.text
        
        dict["purchased_from"]  = strDealer
        dict["purchased_from_name"]  = txtFieldName.text
        dict["purchased_from_area"]  = txtFieldArea.text
        dict["purchased_from_contact_no"]  = txtFieldContactNumber.text
        dict["comments"]  = txtViewComments.text
        
        let imgData = imgSite.jpegData(compressionQuality: 0.2)
        
        SVProgressHUD.show()
//        Alamofire.upload(multipartFormData: { multipartFormData in
//            multipartFormData.append(imgData ?? Data(), withName: "verified_site_image",fileName: "file.jpg", mimeType: "image/jpg")
//            for (key, value) in dict {
//                multipartFormData.append(value.data(using: String.Encoding.utf8)!, withName: key)
//            } //Optional for extra parameters
//        },to:"https://www.starstellar.com/ws_add_site_recommendation_for_te.php")
//        { (result) in
//            switch result {
//            case .success(let upload, _, _):
//                
//                upload.responseJSON {[self] response in
//                    
//                    print(response.result.value!)
//                    SVProgressHUD.dismiss()
//                    let json = JSON(response.value as? [AnyHashable : Any] as Any)
//                    print(json)
//                    
//                    let strStatus = json["process_status"].stringValue
//                    let strMsg = json["process_message"].stringValue
//                    
//                    
//                    if strStatus == "YES" {
//                        showMsgAlert(strMsg)
//                    }else{
//                        if json["is_show_approval_btn"].stringValue == "YES" {
//                            showMailAlert(strMsg, json["approval_btn_text"].stringValue, json["the_recommended_id"].stringValue)
//                        }else{
//                            showMsgAlert(strMsg)
//                        }
//                    }
//                }
//                
//            case .failure(let encodingError):
//                print(encodingError)
//                self.showToastAlert(encodingError.localizedDescription)
//            }
//        }
        
        AF.upload(
            multipartFormData: { multipartFormData in
                if let imgData = imgData {
                    multipartFormData.append(imgData, withName: "verified_site_image", fileName: "file.jpg", mimeType: "image/jpg")
                }
                for (key, value) in dict {
                    if let data = value.data(using: .utf8) {
                        multipartFormData.append(data, withName: key)
                    }
                }
            },
            to: "https://www.starstellar.com/ws_add_site_recommendation_for_te.php",
            method: .post
        )
        .responseJSON { [self] response in
            SVProgressHUD.dismiss()
            switch response.result {
            case .success(let value):
                let json = JSON(value)
                print(json)
                
                let strStatus = json["process_status"].stringValue
                let strMsg = json["process_message"].stringValue
                
                if strStatus == "YES" {
                    showMsgAlert(strMsg)
                } else {
                    if json["is_show_approval_btn"].stringValue == "YES" {
                        showMailAlert(
                            strMsg,
                            json["approval_btn_text"].stringValue,
                            json["the_recommended_id"].stringValue
                        )
                    } else {
                        showMsgAlert(strMsg)
                    }
                }
                
            case .failure(let error):
                print(error.localizedDescription)
                self.showToastAlert(error.localizedDescription)
            }
        }
    }
    
    //MARK: - Helper Method
    
    @objc func resignKeyboard() {
        self.view.endEditing(true)
    }
    
    func openCamera() {
        if(UIImagePickerController .isSourceTypeAvailable(UIImagePickerController.SourceType.camera)) {
            imagePicker.sourceType = UIImagePickerController.SourceType.camera
            imagePicker.allowsEditing = true
            self.present(imagePicker, animated: true, completion: nil)
        }else{
            let alert  = UIAlertController(title: "Warning", message: "You don't have camera", preferredStyle: .alert)
            alert.addAction(UIAlertAction(title: "OK", style: .default, handler: nil))
            self.present(alert, animated: true, completion: nil)
        }
    }
    
    func openGallary() {
        imagePicker.sourceType = UIImagePickerController.SourceType.photoLibrary
        imagePicker.allowsEditing = true
        self.present(imagePicker, animated: true, completion: nil)
    }
    
    func showMsgAlert(_ strMsg : String) -> Void {
        let alert = UIAlertController(title: nil, message: strMsg, preferredStyle: .alert)
        alert.addAction(UIAlertAction(title: "Ok", style: .default, handler: {[self] action in
            navigationController?.popViewController(animated: true)
        }))
        present(alert, animated: true)
    }
    
    func showMailAlert(_ strMsg : String, _ strApprovalBtnText : String, _ strRecommendedId : String) -> Void {
        let alert = UIAlertController(title: nil, message: strMsg, preferredStyle: .alert)
        alert.addAction(UIAlertAction(title: strApprovalBtnText, style: .default, handler: {[self] action in
            if isServerReachable(){
                SVProgressHUD.show()
                
                var dict: [String : String] = [:]
                dict["te_code"] = Defaults.teCode()
                dict["r_site_id"] = strRecommendedId
                
                SSParserLayer.callSendMailToASMByTE(dict, handler: {[self] strStatus, strMessage, dictResponse in
                    SVProgressHUD.dismiss()
                    if (strStatus == "YES") {
                        //let json = JSON(dictResponse!)
                        if strStatus == "YES" {
                            let alert = UIAlertController(title: nil, message: strMessage, preferredStyle: .alert)
                            alert.addAction(UIAlertAction(title: "Ok", style: .default, handler: {[self] action in
                                navigationController?.popViewController(animated: true)
                            }))
                            present(alert, animated: true)
                        }else{
                            showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                        }
                    }else{
                        self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                    }
                })
                
            }else{
                showToastAlert(StringConstant.kNoInternet)
            }
        }))
        present(alert, animated: true)
    }
    
    //MARK: - Keyboard Method
    
    @objc func keyboardWillShow(notification:NSNotification){
        
        let info = notification.userInfo
        let kbSize = (info?[UIResponder.keyboardFrameEndUserInfoKey] as AnyObject).cgRectValue.size
        
        let contentInsets = UIEdgeInsets(top: 0.0, left: 0.0, bottom: kbSize.height , right: 0.0)
        scrollViewFP.contentInset = contentInsets
        scrollViewFP.scrollIndicatorInsets = contentInsets
    }
    
    @objc func keyboardWillHide(notification:NSNotification){
        let contentInset:UIEdgeInsets = UIEdgeInsets.zero
        scrollViewFP.contentInset = contentInset
    }
    
    
}

//MARK: - UITextField Delegate

extension TERecommSiteDetailsUpdateLifting : UITextFieldDelegate {
    func textFieldShouldReturn(_ textField: UITextField) -> Bool {
        return textField.resignFirstResponder()
    }
}

//MARK: - UIPickerView Delegate and DataSource

extension TERecommSiteDetailsUpdateLifting : UIPickerViewDelegate, UIPickerViewDataSource {
    
    
    func numberOfComponents(in pickerView: UIPickerView) -> Int {
        return 1
    }
    
    func pickerView(_ pickerView: UIPickerView, numberOfRowsInComponent component: Int) -> Int {
        return arrExpectedProduct.count
    }
    
    func pickerView(_ pickerView: UIPickerView, titleForRow row: Int, forComponent component: Int) -> String?{
        return arrExpectedProduct[row]["prod_name"].stringValue
    }
    
    func pickerView(_ pickerView: UIPickerView, didSelectRow row: Int, inComponent component: Int) {
        txtFieldProduct.text = arrExpectedProduct[row]["prod_name"].stringValue
        txtFieldProduct.accessibilityValue = arrExpectedProduct[row]["prod_id"].stringValue
    }
}

extension TERecommSiteDetailsUpdateLifting:  UIImagePickerControllerDelegate, UINavigationControllerDelegate, UIGestureRecognizerDelegate{
    
    @objc func imagePickerController(_ picker: UIImagePickerController, didFinishPickingMediaWithInfo info: [UIImagePickerController.InfoKey : Any]) {
        if let pickedImage = info[UIImagePickerController.InfoKey.originalImage] as? UIImage {
            imgSite = pickedImage
        }
        wsAddSiteRecommendationForTE()
        dismiss(animated: true, completion: nil)
    }
    
    func imagePickerControllerDidCancel(_ picker: UIImagePickerController) {
        picker.isNavigationBarHidden = false
        self.dismiss(animated: true, completion: nil)
    }
}
