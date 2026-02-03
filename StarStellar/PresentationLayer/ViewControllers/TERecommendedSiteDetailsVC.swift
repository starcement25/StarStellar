//
//  TERecommendedSiteDetailsVC.swift
//  StarStellar
//
//  Created by Apple on 19/08/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit
import SwiftyJSON
import Alamofire
import SVProgressHUD
import MessageUI

class TERecommendedSiteDetailsVC: BaseViewController, UIPickerViewDelegate, UIPickerViewDataSource {
    
    @IBOutlet var viewComment: UIView!
    @IBOutlet weak var txtViewComment: FPTextView!
    @IBOutlet weak var btnSubmit: FPButton!
    @IBOutlet weak var constraintBottomSpace: NSLayoutConstraint!
    @IBOutlet weak var tblViewRL: UITableView!
    var dictDetails : JSON = []
    var intSelectedTab = 0 //To recognise which tab was selected in previous view controller
    @IBOutlet weak var imgViewSite: UIImageView!
    
    var imgSite = UIImage()
    
    var arrSection : [String] = []
    
    var arrFirstSectionLabel : [String] = []
    var arrSecondSectionLabel : [String] = []
    var arrThirdSectionLabel : [String] = []
    
    var arrFirstSectionValue : [String] = []
    var arrSecondSectionValue : [String] = []
    var arrThirdSectionValue : [String] = []
    
    var arrLabel = [Array<Any>]()
    var arrValue = [Array<Any>]()
    
    var imagePicker = UIImagePickerController()
    
    var arrExpectedProduct       = [JSON]()
    var pickerExpectedProduct: UIPickerView? = nil
    var strDealer = ""
    
    
    //MARK: - View Life Cycle
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.designView()
        self.loadData()
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        NotificationCenter.default.addObserver(self, selector: #selector(keyboardWillShow(_:)), name: UIResponder.keyboardWillShowNotification, object: nil)
        NotificationCenter.default.addObserver(self, selector: #selector(keyboardWillHide(_:)), name: UIResponder.keyboardWillHideNotification, object: nil)
    }
    
    override func viewWillDisappear(_ animated: Bool) {
        super.viewWillDisappear(animated)
        
        NotificationCenter.default.removeObserver(self, name: UIResponder.keyboardWillShowNotification, object: nil)
        NotificationCenter.default.removeObserver(self, name: UIResponder.keyboardWillHideNotification, object: nil)
    }
    
    //MARK: - Initialization Method
    
    func designView() -> Void {
        
        print("-->>",intSelectedTab)
        
        imagePicker.delegate = self
        
        constraintBottomSpace.constant = intSelectedTab == 101 ? 50 : 0;
        
        tblViewRL.rowHeight = UITableView.automaticDimension
        //tblViewRL.estimatedRowHeight = 256.0
        tblViewRL.estimatedRowHeight = 44.0
        
        tblViewRL.register(UINib(nibName: "MySiteDetailsCell", bundle: nil), forCellReuseIdentifier: "cell")
        tblViewRL.register(UINib(nibName: "DetailsOfProductCell", bundle: nil), forCellReuseIdentifier: "DetailsOfProductCell")
        tblViewRL.separatorColor = .clear
    }
    
    func loadData() -> Void {
        
        if intSelectedTab == 101 {
            
            // PENDING
            
            strDealer = "Dealer"
            
            arrSection = ["DETAILS OF RECOMMENDATION","ENGINEER DETAILS","DETAILS OF PRODUCT"]
            
            arrFirstSectionLabel = ["Site name:","Site Address:","Site Potential","Contact Person:","Category:","Mobile:"]
            arrSecondSectionLabel = ["Recommended By:","Contact No:","Email:"]
            arrThirdSectionLabel = [""]
            
            
            print(dictDetails)
            
            arrFirstSectionValue = [dictDetails["r_site_name"].stringValue,
                                    dictDetails["r_address"].stringValue,
                                    dictDetails["r_site_potential_in_mt"].stringValue,
                                    dictDetails["r_contact_person_name"].stringValue,
                                    dictDetails["r_contact_person_category_name"].stringValue,
                                    dictDetails["r_mobile_no"].stringValue];
            
            arrSecondSectionValue = [dictDetails["r_recomended_by"].stringValue,
                                     dictDetails["r_contact_no"].stringValue,
                                     dictDetails["r_email"].stringValue];
            
            arrThirdSectionValue = [""]
            
            arrLabel = [arrFirstSectionLabel,arrSecondSectionLabel,arrThirdSectionLabel]
            arrValue = [arrFirstSectionValue,arrSecondSectionValue,arrThirdSectionValue]
            
            getExpectedProduct()
            
            
        }else if intSelectedTab == 102 {
            
            //APPROVED
            arrSection = ["DETAILS OF RECOMMENDATION","ENGINEER DETAILS","STATUS"]

            arrFirstSectionLabel = ["Site name:","Site Address:","Site Potential","Contact Person:","Category:","Mobile:","Actual Product Name:","Actual Consumption:","Purchased From:","Purchased From Name:","Purchased From Area:","Purchased From Contact No:"]
            arrSecondSectionLabel = ["Recommended By:","Contact No:","Email:"]
            arrThirdSectionLabel = ["Approved Date:","Stellar Points Earned:","Comments:"]
            
            print(dictDetails)
            
            arrFirstSectionValue = [dictDetails["r_site_name"].stringValue,
                                    dictDetails["r_address"].stringValue,
                                    dictDetails["r_site_potential_in_mt"].stringValue,
                                    dictDetails["r_contact_person_name"].stringValue,
                                    dictDetails["r_contact_person_category_name"].stringValue,
                                    dictDetails["r_mobile_no"].stringValue,
                                    dictDetails["actual_product_name"].stringValue,
                                    dictDetails["actual_consumption"].stringValue,
                                    dictDetails["purchased_from"].stringValue,
                                    dictDetails["purchased_from_name"].stringValue,
                                    dictDetails["purchased_from_area"].stringValue,
                                    dictDetails["purchased_from_contact_no"].stringValue];
            
            arrSecondSectionValue = [dictDetails["r_recomended_by"].stringValue,
                                     dictDetails["r_contact_no"].stringValue,
                                     dictDetails["r_email"].stringValue];
            
            arrThirdSectionValue = [dictDetails["approved_date"].stringValue,
                                    dictDetails["stellar_points_earned"].stringValue,
                                    dictDetails["comments"].stringValue];
            
            
            arrLabel = [arrFirstSectionLabel,arrSecondSectionLabel,arrThirdSectionLabel]
            arrValue = [arrFirstSectionValue,arrSecondSectionValue,arrThirdSectionValue]
            
        }else {
            
            //REJECTED
            arrSection = ["DETAILS OF RECOMMENDATION","ENGINEER DETAILS","STATUS"]
            
            arrFirstSectionLabel = ["Site name:","Site Address:","Site Potential","Contact Person:","Category:","Mobile:"]
            arrSecondSectionLabel = ["Recommended By:","Contact No:","Email:"]
            arrThirdSectionLabel = ["Comments:"]
            
            print(dictDetails)
            
            arrFirstSectionValue = [dictDetails["r_site_name"].stringValue,
                                    dictDetails["r_address"].stringValue,
                                    dictDetails["r_site_potential_in_mt"].stringValue,
                                    dictDetails["r_contact_person_name"].stringValue,
                                    dictDetails["r_contact_person_category_name"].stringValue,
                                    dictDetails["r_mobile_no"].stringValue];
            
            arrSecondSectionValue = [dictDetails["r_recomended_by"].stringValue,
                                     dictDetails["r_contact_no"].stringValue,
                                     dictDetails["r_email"].stringValue];
            
            arrThirdSectionValue = [dictDetails["comments"].stringValue];
            
            
            arrLabel = [arrFirstSectionLabel,arrSecondSectionLabel,arrThirdSectionLabel]
            arrValue = [arrFirstSectionValue,arrSecondSectionValue,arrThirdSectionValue]
            
        }        
        
        /*
         arrSection = ["DETAILS OF RECOMMENDATION","ENGINEER DETAILS","STATUS"]
         
         arrFirstSectionLabel = ["Site name:","Site Address:","Site Potential","Contact Person:","Category:","Mobile:"]
         arrSecondSectionLabel = ["Recommended By:","Contact No:","Email:"]
         arrThirdSectionLabel = ["Approved Date:","Stellar Points Earned:","Comments:"]
         
         print(dictDetails)
         
         arrFirstSectionValue = [dictDetails["r_site_name"].stringValue,
         dictDetails["r_address"].stringValue,
         dictDetails["r_site_potential_in_mt"].stringValue,
         dictDetails["r_contact_person_name"].stringValue,
         dictDetails["r_contact_person_category_name"].stringValue,
         dictDetails["r_mobile_no"].stringValue];
         
         arrSecondSectionValue = [dictDetails["r_recomended_by"].stringValue,
         dictDetails["r_contact_no"].stringValue,
         dictDetails["r_email"].stringValue];
         
         arrThirdSectionValue = [dictDetails["approved_date"].stringValue,
         dictDetails["stellar_points_earned"].stringValue,
         dictDetails["comments"].stringValue];
         
         
         arrLabel = [arrFirstSectionLabel,arrSecondSectionLabel,arrThirdSectionLabel]
         arrValue = [arrFirstSectionValue,arrSecondSectionValue,arrThirdSectionValue]
         */
        
        imgViewSite.sd_setImage(with: URL(string: dictDetails["r_recomended_site_image_url"].stringValue), placeholderImage: UIImage(named: "image_placeholder"))
        
    }
    
    //MARK: - IBAction's
    
    @IBAction func btnBackClicked(_ sender: UIBarButtonItem) {
        navigationController?.popViewController(animated: true)
    }
    
    @IBAction func btnConfirmClicked(_ sender: FPButton) {
        
        self.openCamera()
        
//        let alert = UIAlertController(title: nil, message: nil, preferredStyle: .actionSheet)
//        alert.addAction(UIAlertAction(title: "Camera", style: .default, handler: { (_) in
//            self.openCamera()
//            print("User click camera button")
//        }))
//
//        alert.addAction(UIAlertAction(title: "Gallery", style: .default, handler: { (_) in
//            self.openGallary()
//            print("User click gallery button")
//        }))
//
//        alert.addAction(UIAlertAction(title: "Dismiss", style: .cancel, handler: { (_) in
//            print("User click Dismiss button")
//        }))
//
//        self.present(alert, animated: true, completion: {
//            print("completion block")
//        })
    }
    
    @IBAction func btnRejectClicked(_ sender: FPButton) {
        let viewBase = UIView.init(frame: CGRect(x: 0, y: 0, width: view.frame.size.width, height: view.frame.size.height))
        viewBase.backgroundColor = UIColor.black.withAlphaComponent(0.3)
        viewBase.addSubview(viewComment)
        viewComment.center = viewBase.center
        //viewBase.addGestureRecognizer(UITapGestureRecognizer(target: self, action:#selector(self.handleTap(_:))))
        
        let tapGesture = UITapGestureRecognizer(target: self, action: #selector(self.handleTap(_:)))
        tapGesture.delegate = self
        viewBase.addGestureRecognizer(tapGesture)
        
        view.addSubview(viewBase)
        btnSubmit.tag = 102;
    }
    
    @IBAction func btnSubmitClicked(_ sender: FPButton) {
        callConfirmRecommendedSite()
    }
    
    //MARK: - Web Service
    
    func callConfirmRecommendedSite() -> Void {
        
        if btnSubmit.tag == 101 {
            
            let imgData = imgSite.jpegData(compressionQuality: 0.2)
            
            let indexPath = IndexPath(row: 0, section: 2)
            let cell = tblViewRL.cellForRow(at: indexPath) as? DetailsOfProductCell
            
            
            if cell?.txtFieldSelectProduct.text?.trimmingCharacters(in: .whitespaces).count == 0 {
                showToastAlert("Please select expected product")
                return
            }else if cell?.txtFieldExpectedConsumption.text?.trimmingCharacters(in: .whitespaces).count == 0 {
                showToastAlert("Please enter expected consumption")
                return
            }else if cell?.txtFieldName.text?.trimmingCharacters(in: .whitespaces).count == 0 {
                showToastAlert("Please enter name")
                return
            }else if cell?.txtFieldArea.text?.trimmingCharacters(in: .whitespaces).count == 0 {
                showToastAlert("Please enter area")
                return
            }else if !LogicConstant().validateMobileNumber(cell?.txtFieldContactNumber.text) {
                showToastAlert("Please enter valid mobile number")
                return
            }
            
            var dict: [String : String] = [:]
            dict["te_code"]   = Defaults.teCode()
            dict["r_site_id"] = dictDetails["r_site_id"].stringValue
            dict["comments"]  = txtViewComment.text
            dict["actual_product_id"]  = cell?.txtFieldSelectProduct.accessibilityValue
            dict["actual_product_name"]  = cell?.txtFieldSelectProduct.text
            dict["actual_consumption"]  = cell?.txtFieldExpectedConsumption.text
            dict["purchased_from"]  = strDealer
            dict["purchased_from_name"]  = cell?.txtFieldName.text
            dict["purchased_from_area"]  = cell?.txtFieldArea.text
            dict["purchased_from_contact_no"]  = cell?.txtFieldContactNumber.text
            
            SVProgressHUD.show()
//            Alamofire.upload(multipartFormData: { multipartFormData in
//                multipartFormData.append(imgData ?? Data(), withName: "verified_site_image",fileName: "file.jpg", mimeType: "image/jpg")
//                for (key, value) in dict {
//                    multipartFormData.append(value.data(using: String.Encoding.utf8)!, withName: key)
//                } //Optional for extra parameters
//            },to:"https://www.starstellar.com/ws_confirm_recommended_site_for_te.php")
//            { (result) in
//                switch result {
//                case .success(let upload, _, _):
//                    
//                    upload.responseJSON { response in
//                        print(response.result.value!)
//                        SVProgressHUD.dismiss()
//                        self.navigationController?.popViewController(animated: true)                        
//                    }
//                    
//                case .failure(let encodingError):
//                    print(encodingError)
//                    self.showToastAlert(encodingError.localizedDescription)
//                }
//            }
            
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
                to: "https://www.starstellar.com/ws_confirm_recommended_site_for_te.php",
                method: .post
            )
            .responseJSON { response in
                SVProgressHUD.dismiss()
                switch response.result {
                case .success(let value):
                    print(value)
                    self.navigationController?.popViewController(animated: true)
                case .failure(let error):
                    print(error.localizedDescription)
                    self.showToastAlert(error.localizedDescription)
                }
            }
        }else{
            
            if isServerReachable() {
                
                var dict: [String : String] = [:]
                dict["te_code"]   = Defaults.teCode()
                dict["r_site_id"] = dictDetails["r_site_id"].stringValue
                dict["comments"]  = txtViewComment.text
                
                SVProgressHUD.show()
                SSParserLayer.callRejectedRecommendedSiteByTE(dict) { (strStatus, strMessage, dictResponse) in
                    SVProgressHUD.dismiss()
                    if strStatus == "YES" {
                        self.navigationController?.popViewController(animated: true)
                        self.showToastAlert(strMessage!)
                    }else{
                        self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                    }
                }
                
            }else{
                showToastAlert(StringConstant.kNoInternet)
            }
            
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
        return arrExpectedProduct.count
    }
    
    func pickerView(_ pickerView: UIPickerView, titleForRow row: Int, forComponent component: Int) -> String?{
        return arrExpectedProduct[row]["prod_name"].stringValue
    }
    
    func pickerView(_ pickerView: UIPickerView, didSelectRow row: Int, inComponent component: Int) {
       
        let indexPath = IndexPath(row: 0, section: 2)
        let cell = tblViewRL.cellForRow(at: indexPath) as? DetailsOfProductCell
        
        cell?.txtFieldSelectProduct.text = arrExpectedProduct[row]["prod_name"].stringValue
        cell?.txtFieldSelectProduct.accessibilityValue = arrExpectedProduct[row]["prod_id"].stringValue   

    }
    
    
    // MARK: - Keyboard Method
    
    @objc func keyboardWillShow(_ notification:Notification) {
        
        if let keyboardSize = (notification.userInfo?[UIResponder.keyboardFrameBeginUserInfoKey] as? NSValue)?.cgRectValue {
            tblViewRL.contentInset = UIEdgeInsets(top: 0, left: 0, bottom: keyboardSize.height, right: 0)
        }
    }
    @objc func keyboardWillHide(_ notification:Notification) {
        if let keyboardSize = (notification.userInfo?[UIResponder.keyboardFrameBeginUserInfoKey] as? NSValue)?.cgRectValue {
            tblViewRL.contentInset = UIEdgeInsets(top: 0, left: 0, bottom: 0, right: 0)
        }
    }
    
    
    //MARK: - Helper Method
    
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
    
}

extension TERecommendedSiteDetailsVC : UITableViewDelegate, UITableViewDataSource, MFMailComposeViewControllerDelegate, UITextFieldDelegate {
    
    func numberOfSections(in tableView: UITableView) -> Int {
        //return intSelectedTab == 101 ? 2 : 3;
        return 3;
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrLabel[section].count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        //let cell = tableView.dequeueReusableCell(withIdentifier: "cell", for: indexPath) as? MySiteDetailsCell
        if intSelectedTab == 101{
            if indexPath.section == 2 {
                let cell = tableView.dequeueReusableCell(withIdentifier: "DetailsOfProductCell", for: indexPath) as? DetailsOfProductCell
                
                cell?.txtFieldSelectProduct.delegate = self
                cell?.txtFieldExpectedConsumption.delegate = self
                cell?.txtFieldName.delegate = self
                cell?.txtFieldArea.delegate = self
                cell?.txtFieldContactNumber.delegate = self
                
                cell?.btnDealer.addTarget(self, action: #selector(btnRadioClicked(_:)), for: UIControl.Event.touchUpInside);
                cell?.btnSubdealer.addTarget(self, action: #selector(btnRadioClicked(_:)), for: UIControl.Event.touchUpInside);
                
                // Creating picker for Expected product to be used
                //pickerExpectedProduct = UIPickerView(frame: CGRect(x: 0, y: 0, width: view.frame.width, height: 216))
                pickerExpectedProduct = UIPickerView()
                pickerExpectedProduct?.backgroundColor = UIColor.white
                pickerExpectedProduct?.showsSelectionIndicator = true
                pickerExpectedProduct?.delegate = self
                pickerExpectedProduct?.dataSource = self
                
                cell?.txtFieldSelectProduct.inputView = pickerExpectedProduct
                
                return cell!
            }else{
                let cell = tableView.dequeueReusableCell(withIdentifier: "cell", for: indexPath) as? MySiteDetailsCell
                cell?.lblStatic.text = arrLabel[indexPath.section][indexPath.row] as? String
                cell?.lblValue.text = arrValue[indexPath.section][indexPath.row] as? String
                return cell!
            }
        }else{
            let cell = tableView.dequeueReusableCell(withIdentifier: "cell", for: indexPath) as? MySiteDetailsCell
            cell?.lblStatic.text = arrLabel[indexPath.section][indexPath.row] as? String
            cell?.lblValue.text = arrValue[indexPath.section][indexPath.row] as? String
            return cell!
        }
        //return cell!
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        
        print(indexPath)
        
        if indexPath.section == 0 && indexPath.row == 1 {
            //address
            
            var strAddress = arrValue[indexPath.section][indexPath.row] as? String
            strAddress = strAddress?.replacingOccurrences(of: "\n", with: "").replacingOccurrences(of: ",", with: " ")
            if let addresswithPercentEscapes = strAddress?.addingPercentEncoding(withAllowedCharacters: .urlPathAllowed) {
                
                let urlwithPercentEscapes = "http://maps.google.com/maps?q=\(addresswithPercentEscapes)"
                let url = URL(string: urlwithPercentEscapes)
                if let url = url {
                    if UIApplication.shared.canOpenURL(url) {
                        UIApplication.shared.open(url, options: [:], completionHandler:nil)
                    }
                }
            }
            
            
        }else if indexPath.section == 0 && indexPath.row == 5 {
            //Mobile
            
            let strMobile = arrValue[indexPath.section][indexPath.row]
            if let url = URL(string: "tel://\(strMobile)"),
                UIApplication.shared.canOpenURL(url) {
                if #available(iOS 10, *) {
                    UIApplication.shared.open(url, options: [:], completionHandler:nil)
                } else {
                    UIApplication.shared.openURL(url)
                }
            } else {
                showToastAlert(StringConstant.kErrorMsg)
            }
            
            
        }else if indexPath.section == 1 && indexPath.row == 1 {
            //Contact Number
            let strMobile = arrValue[indexPath.section][indexPath.row]
            if let url = URL(string: "tel://\(strMobile)"),
                UIApplication.shared.canOpenURL(url) {
                if #available(iOS 10, *) {
                    UIApplication.shared.open(url, options: [:], completionHandler:nil)
                } else {
                    UIApplication.shared.openURL(url)
                }
            } else {
                showToastAlert(StringConstant.kErrorMsg)
            }
        }else if indexPath.section == 1 && indexPath.row == 2 {
            //Email
            let email = arrValue[indexPath.section][indexPath.row] as? String
            
            if MFMailComposeViewController.canSendMail() {
                
                let mailComposeViewController = MFMailComposeViewController()
                mailComposeViewController.mailComposeDelegate = self
                mailComposeViewController.setToRecipients([email ?? ""])
                mailComposeViewController.setSubject("Subject")
                mailComposeViewController.setMessageBody("Hello!!!", isHTML: false)
                
                present(mailComposeViewController, animated: true, completion: nil)
                
            }
        }
    }
    
    //    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
    //        return UITableView.automaticDimension
    //    }
    
    func tableView(_ tableView: UITableView, heightForHeaderInSection section: Int) -> CGFloat {
        return 28.0
    }
    
    func tableView(_ tableView: UITableView, viewForHeaderInSection section: Int) -> UIView? {
        let view = UIView(frame: CGRect(x: 0, y: 0, width: self.view.frame.size.width, height: 28))
        view.backgroundColor = UIColor.darkGray
        let lblSectionTitle = UILabel(frame: CGRect(x: 10, y: 0, width: self.view.frame.size.width - 20, height: 28))
        lblSectionTitle.text = arrSection[section]
        lblSectionTitle.textColor = .white
        view.addSubview(lblSectionTitle)
        return view
        
    }
    
    //MARK: - Cell Button Action
    
    @objc func btnRadioClicked(_ sender: UIButton) {
        
        let cell = sender.superview?.superview as? DetailsOfProductCell
        cell?.btnDealer.isSelected = false
        cell?.btnSubdealer.isSelected = false
        
        strDealer = sender.titleLabel!.text!
        sender.isSelected = true
    }
    
    //MARK: - MFMailComposeViewControllerDelegate
    
    func mailComposeController(_ controller: MFMailComposeViewController, didFinishWith result: MFMailComposeResult, error: Error?) {
        controller.dismiss(animated: true, completion: nil)
    }
    
    //MARK: - UITextField Delegate
    
    func textFieldShouldReturn(_ textField: UITextField) -> Bool {
        return textField.resignFirstResponder()
    }
    
}

extension TERecommendedSiteDetailsVC:  UIImagePickerControllerDelegate, UINavigationControllerDelegate, UIGestureRecognizerDelegate{
    
    @objc func imagePickerController(_ picker: UIImagePickerController, didFinishPickingMediaWithInfo info: [UIImagePickerController.InfoKey : Any]) {
        if let pickedImage = info[UIImagePickerController.InfoKey.originalImage] as? UIImage {
            imgSite = pickedImage
            btnSubmit.tag = 101
            let viewBase = UIView.init(frame: CGRect(x: 0, y: 0, width: view.frame.size.width, height: view.frame.size.height))
            viewBase.backgroundColor = UIColor.black.withAlphaComponent(0.3)
            viewBase.addSubview(viewComment)
            viewComment.center = viewBase.center
            //viewBase.addGestureRecognizer(UITapGestureRecognizer(target: self, action:#selector(self.handleTap(_:))))
            
            let tapGesture = UITapGestureRecognizer(target: self, action: #selector(self.handleTap(_:)))
            tapGesture.delegate = self
            viewBase.addGestureRecognizer(tapGesture)
            
            view.addSubview(viewBase)
            
            //callConfirmRecommendedSite()
        }
        
        dismiss(animated: true, completion: nil)
    }
    
    func imagePickerControllerDidCancel(_ picker: UIImagePickerController) {
        picker.isNavigationBarHidden = false
        self.dismiss(animated: true, completion: nil)
    }
    
    //MARK: - Gesture
    
    @objc func handleTap(_ sender: UITapGestureRecognizer? = nil) {
        sender?.view?.removeFromSuperview()
    }
    
    func gestureRecognizer(_ gestureRecognizer: UIGestureRecognizer, shouldReceive touch: UITouch) -> Bool {
        return touch.view == gestureRecognizer.view
    }
}

extension TERecommendedSiteDetailsVC : UITextViewDelegate {
    func textView(_ textView: UITextView, shouldChangeTextIn range: NSRange, replacementText text: String) -> Bool {
        if (text == "\n") {
            textView.resignFirstResponder()
        }
        return true
    }
    
    func textViewShouldBeginEditing(_ textView: UITextView) -> Bool{
        UIView.animate(withDuration: 0.25,
                       delay: 0.0,
                       options: UIView.AnimationOptions.curveEaseInOut,
                       animations: { () -> Void in
                        self.viewComment.center.y -= 100
        }, completion: { (finished) -> Void in
            
        })
        return true
    }
    
    func textViewShouldEndEditing(_ textView: UITextView) -> Bool{
        UIView.animate(withDuration: 0.25,
                       delay: 0.0,
                       options: UIView.AnimationOptions.curveEaseInOut,
                       animations: { () -> Void in
                        self.viewComment.center.y += 100
        }, completion: { (finished) -> Void in
            
        })
        return true
    }
    
}


