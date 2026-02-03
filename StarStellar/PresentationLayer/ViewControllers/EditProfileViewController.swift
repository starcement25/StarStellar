//
//  EditProfileViewController.swift
//  StarStellar
//
//  Created by Apple on 23/07/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit
import SVProgressHUD
import SwiftyJSON
import Alamofire

class EditProfileViewController: BaseTableViewController, UITextFieldDelegate {
    
    var viewUpperHeight: Double = 0.0
    var txtFieldUniversal : UITextField? = nil
    var dictProfile : JSON = []
    
    
    @IBOutlet weak var viewUpperHeightConstraint: NSLayoutConstraint!
    @IBOutlet weak var viewUpper: UIView!
    @IBOutlet weak var btnUpdate: FPButton!
    @IBOutlet weak var viewBase: UIView!
    @IBOutlet weak var txtFieldName: UITextField!
    @IBOutlet weak var txtFieldDOB: UITextField!
    @IBOutlet weak var txtFieldDOM: UITextField!
    @IBOutlet weak var txtViewAddress: FPTextView!
    @IBOutlet weak var txtFieldPincode: UITextField!
    @IBOutlet weak var txtFieldState: UITextField!
    @IBOutlet weak var txtFieldCity: UITextField!
    @IBOutlet weak var btnUserImage: FPButton!
    
    var imagePicker = UIImagePickerController()
    
    //MARK: - View Life Cycle
    
    override func viewDidLoad() {
        super.viewDidLoad()
        designView()
        loadData()
    }
    
    //MARK - Initialization Method
    
    func designView() -> Void {
        setupToolbar()
        setupInputView()
        imagePicker.delegate = self
    }
    
    func loadData() -> Void {
        
        txtFieldName.text    = dictProfile["e_name"].stringValue
        txtFieldDOB.text     = dictProfile["e_dob"].stringValue
        txtFieldDOM.text     = dictProfile["e_dom"].stringValue
        txtViewAddress.text  = dictProfile["e_address"].stringValue
        txtFieldPincode.text = dictProfile["e_pin"].stringValue
        txtFieldState.text   = dictProfile["e_state"].stringValue
        txtFieldCity.text    = dictProfile["e_city_town"].stringValue
//        request(dictProfile["e_profile_image"].stringValue, method: .get)
//            .validate()
//            .responseData(completionHandler: { (responseData) in
//                self.btnUserImage.setBackgroundImage(UIImage(data: responseData.data!), for: UIControl.State.normal)                
//            })
        
        AF.request(dictProfile["e_profile_image"].stringValue, method: .get)
            .validate()
            .responseData { responseData in
                switch responseData.result {
                case .success(let data):
                    if let image = UIImage(data: data) {
                        self.btnUserImage.setBackgroundImage(image, for: .normal)
                    }
                case .failure(let error):
                    print("Image download failed: \(error.localizedDescription)")
                }
            }
    }
    
    override func viewDidLayoutSubviews() {
        super.viewDidLayoutSubviews()
        
        self.viewUpperHeightConstraint.constant = CGFloat(viewUpperHeight);
        viewUpper.frame.size.height = CGFloat(viewUpperHeight);
        viewBase.frame = CGRect(x: 0, y: 0, width: self.view.bounds.size.width, height: btnUpdate.frame.origin.y + btnUpdate.frame.size.height + 10)
        tableView.contentSize = CGSize(width: self.view.bounds.size.width, height: viewBase.frame.size.height)
        
    }
    
    //MARK: - IBAction's
    
    @IBAction func btnBackClicked(_ sender: UIBarButtonItem) {
        navigationController?.popViewController(animated: true)
    }    
    
    @IBAction func btnUserImageClicked(_ sender: FPButton) {
        
        let alert = UIAlertController(title: nil, message: "Choose Image", preferredStyle: .actionSheet)
        alert.addAction(UIAlertAction(title: "Camera", style: .default, handler: { (_) in
            self.openCamera()
            print("User click camera button")
        }))
        
        alert.addAction(UIAlertAction(title: "Gallery", style: .default, handler: { (_) in
            self.openGallary()
            print("User click gallery button")
        }))
        
        alert.addAction(UIAlertAction(title: "Dismiss", style: .cancel, handler: { (_) in
            print("User click Dismiss button")
        }))
        
        self.present(alert, animated: true, completion: {
            print("completion block")
        })
    }
    
    @IBAction func btnUpdateClicked(_ sender: FPButton) {
        
        if txtFieldName.text?.trimmingCharacters(in: .whitespaces).count == 0 {
            showToastAlert("Please enter name")
            return
        }else if txtViewAddress.text?.trimmingCharacters(in: .whitespaces).count == 0 {
            showToastAlert("Please enter address")
            return
        }else if txtFieldPincode.text?.trimmingCharacters(in: .whitespaces).count != 6 {
            showToastAlert("Please enter valid pincode")
            return
        }else if txtFieldState.text?.trimmingCharacters(in: .whitespaces).count == 0 {
            showToastAlert("Please enter state")
            return
        }else if txtFieldCity.text?.trimmingCharacters(in: .whitespaces).count == 0 {
            showToastAlert("Please enter city")
            return
        }
        
        let imgData = btnUserImage.backgroundImage(for: UIControl.State.normal)?.jpegData(compressionQuality: 0.2)
        
        var dict: [String : String] = [:]
        dict["the_engineer_id"] = Defaults.engineerId()
        dict["e_name"]          = txtFieldName.text
        dict["e_dob"]           = txtFieldDOB.text
        dict["e_dom"]           = txtFieldDOB.text
        dict["e_address"]       = txtViewAddress.text
        dict["e_pin"]           = txtFieldPincode.text
        dict["e_state"]         = txtFieldState.text
        dict["e_city_town"]     = txtFieldCity.text
        
        SVProgressHUD.show()
//        Alamofire.upload(multipartFormData: { multipartFormData in
//            multipartFormData.append(imgData ?? Data(), withName: "e_profile_image",fileName: "file.jpg", mimeType: "image/jpg")
//            for (key, value) in dict {
//                multipartFormData.append(value.data(using: String.Encoding.utf8)!, withName: key)
//            } //Optional for extra parameters
//        },to:"https://www.starstellar.com/ws_update_profile_details_for_engineer.php")
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
                    multipartFormData.append(imgData, withName: "e_profile_image", fileName: "file.jpg", mimeType: "image/jpg")
                }
                for (key, value) in dict {
                    if let data = value.data(using: .utf8) {
                        multipartFormData.append(data, withName: key)
                    }
                }
            },
            to: "https://www.starstellar.com/ws_update_profile_details_for_engineer.php",
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
    
    func textFieldShouldReturn(_ textField: UITextField) -> Bool{
        return textField.resignFirstResponder()
    }
    
    func textFieldDidBeginEditing(_ textField: UITextField){
        txtFieldUniversal = textField
    }
    
    //MARK: - Helper Method
    
    func setupToolbar() -> Void {
        
        let numberToolbar = UIToolbar(frame:CGRect(x: 0, y: 0, width: UIScreen.main.bounds.width, height: 50))
        numberToolbar.barStyle = .default
        numberToolbar.items = [
            UIBarButtonItem(barButtonSystemItem: .flexibleSpace, target: nil, action: nil),
            UIBarButtonItem(title: "Done", style: .plain, target: self, action: #selector(doneWithNumberPad))]
        numberToolbar.sizeToFit()
        txtFieldDOB.inputAccessoryView = numberToolbar
        txtFieldDOM.inputAccessoryView = numberToolbar
        txtViewAddress.inputAccessoryView = numberToolbar
        txtFieldPincode.inputAccessoryView = numberToolbar
        
    }
    
    func setupInputView() -> Void {
        
        let datePickerView = UIDatePicker()
        datePickerView.datePickerMode = .date
        if #available(iOS 13.4, *) {
            datePickerView.preferredDatePickerStyle = .wheels
        }
        txtFieldDOB.inputView = datePickerView
        txtFieldDOM.inputView = datePickerView
        datePickerView.addTarget(self, action: #selector(handleDatePicker(sender:)), for: .valueChanged)
        
    }
    
    @objc func doneWithNumberPad() {
        self.view.endEditing(true)
    }
    
    @objc func handleDatePicker(sender: UIDatePicker) {
        let dateFormatter = DateFormatter()
        dateFormatter.dateFormat = "dd MMM yyyy"
        txtFieldUniversal?.text = dateFormatter.string(from: sender.date)
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

extension EditProfileViewController:  UIImagePickerControllerDelegate, UINavigationControllerDelegate{
    
    @objc func imagePickerController(_ picker: UIImagePickerController, didFinishPickingMediaWithInfo info: [UIImagePickerController.InfoKey : Any]) {
        if let pickedImage = info[UIImagePickerController.InfoKey.originalImage] as? UIImage {
            btnUserImage.setBackgroundImage(pickedImage, for: UIControl.State.normal)
        }
        
        dismiss(animated: true, completion: nil)
    }
    
    func imagePickerControllerDidCancel(_ picker: UIImagePickerController) {
        picker.isNavigationBarHidden = false
        self.dismiss(animated: true, completion: nil)
    }
}
