//
//  NewSiteRecommendationViewController.swift
//  StarStellar
//
//  Created by Apple on 22/07/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit
import Alamofire
import SVProgressHUD
import SwiftyJSON
import SDWebImage

class NewSiteRecommendationViewController: BaseTableViewController, UITextFieldDelegate, UIPickerViewDelegate, UIPickerViewDataSource {
    
    @IBOutlet weak var txtFieldExistingSite: UITextField!
    @IBOutlet weak var txtFieldSiteName: UITextField!
    @IBOutlet weak var txtFieldContactPersonName: UITextField!
    @IBOutlet weak var txtFieldMobileNumber: UITextField!
    @IBOutlet weak var txtFieldSiteAddress: UITextField!
    @IBOutlet weak var txtFieldSitePotential: UITextField!
    @IBOutlet weak var txtFieldContactPersonCategory: UITextField!
    @IBOutlet weak var txtFieldExpectedProduct: FPTextField!
    @IBOutlet weak var txtFieldExpectedConsumption: FPTextField!
    @IBOutlet weak var btnSitePhoto: FPButton!
    
    var imagePicker = UIImagePickerController()
    var arrContactPersonCategory = [JSON]()
    var arrExpectedProduct       = [JSON]()
    var arrMyRecommendedSite     = [JSON]()
    var pickerPersonCategory: UIPickerView? = nil
    var pickerExpectedProduct: UIPickerView? = nil
    var pickerMyRecommendedSite: UIPickerView? = nil
    var imgSite = UIImage()
    var strExistingSiteId = ""
    
    //MARK: - View Life Cycle
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.designView()
        self.loadData()
    }
    
    override func viewWillAppear(_ animated: Bool) {
        navigationController?.setNavigationBarHidden(false, animated: true)
    }
    
    //MARK: - Initialization Method
    
    func designView() -> Void {
        setupToolbar()
        imagePicker.delegate = self
    }
    
    func loadData() -> Void {
        getContactPersonCategory()
        getExpectedProduct()
        getMyRecommendedSites()
    }
    
    //MARK: - IBAction's
    
    @IBAction func btnBackClicked(_ sender: UIBarButtonItem) {
        navigationController?.popViewController(animated: true)
    }
    
    @IBAction func btnSitePhotoClicked(_ sender: FPButton) {
        
        self.openCamera()
        
        //            let alert = UIAlertController(title: nil, message: "Choose Image", preferredStyle: .actionSheet)
        //            alert.addAction(UIAlertAction(title: "Camera", style: .default, handler: { (_) in
        //                self.openCamera()
        //                print("User click camera button")
        //            }))
        //
        //            alert.addAction(UIAlertAction(title: "Gallery", style: .default, handler: { (_) in
        //                self.openGallary()
        //                print("User click gallery button")
        //            }))
        //
        //            alert.addAction(UIAlertAction(title: "Dismiss", style: .cancel, handler: { (_) in
        //                print("User click Dismiss button")
        //            }))
        //
        //            self.present(alert, animated: true, completion: {
        //                print("completion block")
        //            })
        
    }
    
    @IBAction func btnSubmitClicked(_ sender: FPButton) {
        
        let imgData = imgSite.jpegData(compressionQuality: 0.2)
        
        if txtFieldSiteName.text?.trimmingCharacters(in: .whitespaces).count == 0 {
            showToastAlert("Please enter site name")
            return
        }else if txtFieldContactPersonName.text?.trimmingCharacters(in: .whitespaces).count == 0 {
            showToastAlert("Please enter contact person name")
            return
        }else if !LogicConstant().validateMobileNumber(txtFieldMobileNumber.text) {
            showToastAlert("Please enter valid mobile number")
            return
        }else if txtFieldSiteAddress.text?.trimmingCharacters(in: .whitespaces).count == 0 {
            showToastAlert("Please enter address")
            return
        }else if txtFieldSitePotential.text?.trimmingCharacters(in: .whitespaces).count == 0 {
            showToastAlert("Please enter site potential")
            return
        }else if txtFieldContactPersonCategory.text?.trimmingCharacters(in: .whitespaces).count == 0 {
            showToastAlert("Please enter contact person category")
            return
        }else if txtFieldExpectedProduct.text?.trimmingCharacters(in: .whitespaces).count == 0 {
            showToastAlert("Please enter expected product")
            return
        }else if txtFieldExpectedConsumption.text?.trimmingCharacters(in: .whitespaces).count == 0 {
            showToastAlert("Please enter expected consumption")
            return
        }
        
        //            else if imgData == nil {
        //                showToastAlert("Please capture site image")
        //                return
        //            }
        
        //let imgData = imgSite.jpegData(compressionQuality: 0.2)
        
        var dict: [String : String] = [:]
        dict["existing_id"]                  = Defaults.teCode()
        dict["te_code"]                      = Defaults.teCode()
        dict["the_engineer_id"]              = Defaults.engineerId()
        dict["site_name"]                    = txtFieldSiteName.text
        dict["contact_person_name"]          = txtFieldContactPersonName.text
        dict["mobile_no"]                    = txtFieldMobileNumber.text
        dict["address"]                      = txtFieldSiteAddress.text
        dict["site_potential_in_mt"]         = txtFieldSitePotential.text
        dict["contact_person_category_name"] = txtFieldContactPersonCategory.text
        dict["expected_product_id"]          = txtFieldExpectedProduct.accessibilityValue
        dict["expected_consumption"]         = txtFieldExpectedConsumption.text
        
        SVProgressHUD.show()
//        Alamofire.upload(multipartFormData: { multipartFormData in
//            multipartFormData.append(imgData ?? Data(), withName: "recomended_site_image",fileName: "file.jpg", mimeType: "image/jpg")
//            for (key, value) in dict {
//                multipartFormData.append(value.data(using: String.Encoding.utf8)!, withName: key)
//            } //Optional for extra parameters
//        },to:"https://www.starstellar.com/ws_add_site_recommendation_for_engineer.php")
//        { (result) in
//            switch result {
//            case .success(let upload, _, _):
//                
//                upload.uploadProgress(closure: { (progress) in
//                    print("Upload Progress: \(progress.fractionCompleted)")
//                    SVProgressHUD.showProgress(Float(progress.fractionCompleted))
//                    //SVProgressHUD.show(progress: CGFloat(progress.fractionCompleted))
//                })
//                
//                upload.responseJSON { response in
//                    print(response.result.value!)
//                    SVProgressHUD.dismiss()
//                    self.navigationController?.popViewController(animated: true)
//                }
//                
//            case .failure(let encodingError):
//                print(encodingError)
//            }
//        }
        
        AF.upload(
            multipartFormData: { multipartFormData in
                if let imgData = imgData {
                    multipartFormData.append(imgData, withName: "recomended_site_image", fileName: "file.jpg", mimeType: "image/jpg")
                }
                for (key, value) in dict {
                    if let data = value.data(using: .utf8) {
                        multipartFormData.append(data, withName: key)
                    }
                }
            },
            to: "https://www.starstellar.com/ws_add_site_recommendation_for_engineer.php",
            method: .post
        )
        .uploadProgress { progress in
            print("Upload Progress: \(progress.fractionCompleted)")
            SVProgressHUD.showProgress(Float(progress.fractionCompleted))
        }
        .responseJSON { response in
            SVProgressHUD.dismiss()
            switch response.result {
            case .success(let value):
                print(value)
                self.navigationController?.popViewController(animated: true)
            case .failure(let error):
                print(error.localizedDescription)
            }
        }
    }
    
    //MARK: - UITextField Delegate
    
    func textFieldDidEndEditing(_ textField: UITextField) {
        if textField == txtFieldExistingSite{
            if txtFieldExistingSite.accessibilityValue != nil{
                //print(txtFieldExistingSite.accessibilityValue)
                let row = Int(txtFieldExistingSite.accessibilityHint ?? "") ?? 0
                strExistingSiteId = arrMyRecommendedSite[row]["r_site_id"].stringValue
                txtFieldSiteName.text = arrMyRecommendedSite[row]["r_site_name"].stringValue
                txtFieldSiteAddress.text = arrMyRecommendedSite[row]["r_address"].stringValue
                txtFieldSitePotential.text = arrMyRecommendedSite[row]["r_site_potential_in_mt"].stringValue
                txtFieldContactPersonName.text = arrMyRecommendedSite[row]["r_contact_person_name"].stringValue
                txtFieldContactPersonCategory.text = arrMyRecommendedSite[row]["r_contact_person_category_name"].stringValue
                txtFieldMobileNumber.text = arrMyRecommendedSite[row]["r_mobile_no"].stringValue
                
                txtFieldSiteName.isUserInteractionEnabled = false
                txtFieldSiteAddress.isUserInteractionEnabled = false
                txtFieldSitePotential.isUserInteractionEnabled = false
                txtFieldContactPersonName.isUserInteractionEnabled = false
                txtFieldContactPersonCategory.isUserInteractionEnabled = false
                txtFieldSiteName.isUserInteractionEnabled = false
                
                //txtFieldExpectedProduct.text = arrMyRecommendedSite[row]["expected_product_name"].stringValue
                //txtFieldExpectedProduct.accessibilityValue = arrMyRecommendedSite[row]["expected_product_id"].stringValue
                //txtFieldExpectedConsumption.text = arrMyRecommendedSite[row]["expected_consumption"].stringValue
                //btnSitePhoto.sd_setBackgroundImage(with: URL(string: arrMyRecommendedSite[row]["r_recomended_site_image_url"].stringValue), for: .normal, placeholderImage:nil)
                btnSitePhoto.sd_setBackgroundImage(with: URL(string: arrMyRecommendedSite[row]["r_recomended_site_image_url"].stringValue), for: .normal) { [self] img, _, _, _ in
                    imgSite = img ?? UIImage()
                }
            }
        }
    }
    
    func textFieldShouldBeginEditing(_ textField: UITextField) -> Bool {
        if txtFieldExistingSite.text?.count != 0 {
            if !txtFieldSiteName.isUserInteractionEnabled{
                if textField == txtFieldExpectedProduct {
                    return true
                }else if textField == txtFieldExpectedConsumption {
                    return true
                }else if textField == txtFieldExistingSite {
                    return true
                }
            }
            return false
        }
        return true
    }
    
    func textFieldShouldReturn(_ textField: UITextField) -> Bool{
        return textField.resignFirstResponder()
    }
    
    //MARK: - WebService
    
    func getMyRecommendedSites() -> Void {
        
        if isServerReachable(){
            
            var dict: [String : String] = [:]
            dict["the_engineer_id"] = Defaults.engineerId()
            
            SVProgressHUD.show()
            SSParserLayer.callShowMyRecommendedSites(dict, handler: { strStatus, strMessage, dictResponse in
                SVProgressHUD.dismiss()
                if (strStatus == "YES") {
                    let json = JSON(dictResponse!)
                    print(json)
                    self.arrMyRecommendedSite = json["my_recommended_site_data"].arrayValue
                    self.pickerMyRecommendedSite?.reloadAllComponents()
                }else{
                    self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                }
            })
            
        }else{
            showToastAlert(StringConstant.kNoInternet)
        }
    }
    
    func getContactPersonCategory() -> Void {
        
        if isServerReachable(){
            
            SVProgressHUD.show()
            SSParserLayer.callContactPersonCategoryForEngineer(nil, handler: { strStatus, strMessage, dictResponse in
                SVProgressHUD.dismiss()
                if (strStatus == "YES") {
                    
                    let json = JSON(dictResponse!)
                    self.arrContactPersonCategory = json["contact_person_category_data"].arrayValue
                    print(self.arrContactPersonCategory)
                    self.pickerPersonCategory?.reloadAllComponents()
                    
                }else{
                    self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                }
            })
            
        }else{
            showToastAlert(StringConstant.kNoInternet)
        }
    }
    
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
    
    //MARK: - UIPickerView Delegate and DataSource
    
    func numberOfComponents(in pickerView: UIPickerView) -> Int {
        return 1
    }
    
    func pickerView(_ pickerView: UIPickerView, numberOfRowsInComponent component: Int) -> Int {
        
        if pickerView == pickerPersonCategory {
            return arrContactPersonCategory.count
        }else if pickerView == pickerMyRecommendedSite{
            return arrMyRecommendedSite.count
        }else{
            return arrExpectedProduct.count
        }
    }
    
    func pickerView(_ pickerView: UIPickerView, titleForRow row: Int, forComponent component: Int) -> String?{
        if pickerView == pickerPersonCategory {
            return arrContactPersonCategory[row].stringValue
        }else if pickerView == pickerMyRecommendedSite{
            return arrMyRecommendedSite[row]["r_site_name"].stringValue
        }else{
            return arrExpectedProduct[row]["prod_name"].stringValue
        }
    }
    
    func pickerView(_ pickerView: UIPickerView, didSelectRow row: Int, inComponent component: Int) {
        if pickerView == pickerPersonCategory {
            txtFieldContactPersonCategory.text = arrContactPersonCategory[row].stringValue
        }else if pickerView == pickerMyRecommendedSite{
            txtFieldExistingSite.text = arrMyRecommendedSite[row]["r_site_name"].stringValue
            txtFieldExistingSite.accessibilityValue = arrMyRecommendedSite[row]["r_site_id"].stringValue
            txtFieldExistingSite.accessibilityHint = String(row)
        }else{
            txtFieldExpectedProduct.text = arrExpectedProduct[row]["prod_name"].stringValue
            txtFieldExpectedProduct.accessibilityValue = arrExpectedProduct[row]["prod_id"].stringValue
        }
    }
    
    //MARK: - Helper Method
    
    func setupToolbar() -> Void {
        //let numberToolbar = UIToolbar(frame:CGRect(x: 0, y: 0, width: UIScreen.main.bounds.width, height: 50))
        let doneToolbar = UIToolbar()
        doneToolbar.barStyle = .default
        doneToolbar.items = [
            UIBarButtonItem(barButtonSystemItem: .flexibleSpace, target: nil, action: nil),
            UIBarButtonItem(title: "Done", style: .plain, target: self, action: #selector(doneWithNumberPad))]
        doneToolbar.sizeToFit()
        txtFieldMobileNumber.inputAccessoryView = doneToolbar
        txtFieldSitePotential.inputAccessoryView = doneToolbar
        txtFieldContactPersonCategory.inputAccessoryView = doneToolbar
        txtFieldExpectedProduct.inputAccessoryView = doneToolbar
        txtFieldExpectedConsumption.inputAccessoryView = doneToolbar
        txtFieldExistingSite.inputAccessoryView = doneToolbar
        
        // Creating picker for contact person category
        //pickerPersonCategory = UIPickerView(frame: CGRect(x: 0, y: 0, width: view.frame.width, height: 216))
        pickerPersonCategory = UIPickerView()
        pickerPersonCategory?.backgroundColor = UIColor.white
        pickerPersonCategory?.showsSelectionIndicator = true
        pickerPersonCategory?.delegate = self
        pickerPersonCategory?.dataSource = self
        
        // Creating picker for Expected product to be used
        //pickerExpectedProduct = UIPickerView(frame: CGRect(x: 0, y: 0, width: view.frame.width, height: 216))
        pickerExpectedProduct = UIPickerView()
        pickerExpectedProduct?.backgroundColor = UIColor.white
        pickerExpectedProduct?.showsSelectionIndicator = true
        pickerExpectedProduct?.delegate = self
        pickerExpectedProduct?.dataSource = self
        
        // Creating picker for my recommended site
        pickerMyRecommendedSite = UIPickerView()
        pickerMyRecommendedSite?.backgroundColor = UIColor.white
        pickerMyRecommendedSite?.showsSelectionIndicator = true
        pickerMyRecommendedSite?.delegate = self
        pickerMyRecommendedSite?.dataSource = self
        
        txtFieldContactPersonCategory.inputView = pickerPersonCategory
        txtFieldExpectedProduct.inputView = pickerExpectedProduct
        txtFieldExistingSite.inputView = pickerMyRecommendedSite
        
    }
    
    @objc func doneWithNumberPad() {
        
        self.view.endEditing(true)
    }
    
    func openCamera() {
        if(UIImagePickerController .isSourceTypeAvailable(UIImagePickerController.SourceType.camera))
        {
            imagePicker.sourceType = UIImagePickerController.SourceType.camera
            imagePicker.allowsEditing = true
            self.present(imagePicker, animated: true, completion: nil)
        }
        else
        {
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
}

//MARK: - UIImagePickerControllerDelegate

extension NewSiteRecommendationViewController:  UIImagePickerControllerDelegate, UINavigationControllerDelegate{
    
    @objc func imagePickerController(_ picker: UIImagePickerController, didFinishPickingMediaWithInfo info: [UIImagePickerController.InfoKey : Any]) {
        if let pickedImage = info[UIImagePickerController.InfoKey.originalImage] as? UIImage {
            imgSite = pickedImage
            btnSitePhoto.setBackgroundImage(pickedImage, for: UIControl.State.normal)
        }
        
        dismiss(animated: true, completion: nil)
    }
    
    func imagePickerControllerDidCancel(_ picker: UIImagePickerController) {
        picker.isNavigationBarHidden = false
        self.dismiss(animated: true, completion: nil)
    }
}
