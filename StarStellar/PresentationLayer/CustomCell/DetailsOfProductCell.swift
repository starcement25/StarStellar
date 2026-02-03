//
//  DetailsOfProductCell.swift
//  StarStellar
//
//  Created by Apple on 14/11/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit

class DetailsOfProductCell: UITableViewCell {

    @IBOutlet weak var txtFieldSelectProduct: FPTextField!
    @IBOutlet weak var txtFieldExpectedConsumption: FPTextField!
    @IBOutlet weak var btnDealer: UIButton!
    @IBOutlet weak var btnSubdealer: UIButton!
    @IBOutlet weak var txtFieldName: FPTextField!
    @IBOutlet weak var txtFieldArea: FPTextField!
    @IBOutlet weak var txtFieldContactNumber: FPTextField!
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
        setupToolbar()
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }
    
    func setupToolbar() -> Void {
        //let numberToolbar = UIToolbar(frame:CGRect(x: 0, y: 0, width: UIScreen.main.bounds.width, height: 50))
        let doneToolbar = UIToolbar()
        doneToolbar.barStyle = .default
        doneToolbar.items = [
            UIBarButtonItem(barButtonSystemItem: .flexibleSpace, target: nil, action: nil),
            UIBarButtonItem(title: "Done", style: .plain, target: self, action: #selector(doneWithNumberPad))]
        doneToolbar.sizeToFit()
        self.txtFieldSelectProduct.inputAccessoryView = doneToolbar
        self.txtFieldExpectedConsumption.inputAccessoryView = doneToolbar
        self.txtFieldContactNumber.inputAccessoryView = doneToolbar
        
        
//        // Creating picker for contact person category
//        //pickerPersonCategory = UIPickerView(frame: CGRect(x: 0, y: 0, width: view.frame.width, height: 216))
//        pickerPersonCategory = UIPickerView()
//        pickerPersonCategory?.backgroundColor = UIColor.white
//        pickerPersonCategory?.showsSelectionIndicator = true
//        pickerPersonCategory?.delegate = self
//        pickerPersonCategory?.dataSource = self
//
//        // Creating picker for Expected product to be used
//        //pickerExpectedProduct = UIPickerView(frame: CGRect(x: 0, y: 0, width: view.frame.width, height: 216))
//        pickerExpectedProduct = UIPickerView()
//        pickerExpectedProduct?.backgroundColor = UIColor.white
//        pickerExpectedProduct?.showsSelectionIndicator = true
//        pickerExpectedProduct?.delegate = self
//        pickerExpectedProduct?.dataSource = self
//
//        txtFieldContactPersonCategory.inputView = pickerPersonCategory
//        txtFieldExpectedProduct.inputView = pickerExpectedProduct
        
    }
    
    @objc func doneWithNumberPad() {
        self.endEditing(true)
    }
    
    
}
