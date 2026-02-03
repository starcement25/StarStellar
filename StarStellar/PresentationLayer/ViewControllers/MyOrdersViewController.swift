//
//  MyOrdersViewController.swift
//  StarStellar
//
//  Created by Apple on 07/08/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit
import Alamofire
import SVProgressHUD
import SwiftyJSON
import SDWebImage


class MyOrdersViewController: BaseViewController {
    
    //Support View
    //-------------------------------
    @IBOutlet var viewSupport: FPView!
    @IBOutlet var btnSupportRadioGroup: [UIButton]!
    @IBOutlet weak var txtViewSupport: FPTextView!
    var strSupportType = ""
    var strOrderId = ""
    //-------------------------------
    
    //Redeem View
    //-------------------------------
    @IBOutlet var viewReedem: FPView!
    @IBOutlet weak var lblRedeem: UILabel!
    @IBOutlet weak var btnRedeem: UIButton!
    
    //-------------------------------
    
    //Received View
    //-------------------------------
    @IBOutlet var viewReceived: UIView!
    @IBOutlet var btnReceivedRadioGroup: [UIButton]!
    var strReceivedType = ""
    
    //-------------------------------
    
    @IBOutlet weak var tblViewPending: UITableView!
    @IBOutlet weak var tblViewDelivered: UITableView!
    @IBOutlet var btnTabs: [UIButton]!
    
    var arrPendingOrders : [JSON] = []
    var arrDeliveredOrders : [JSON] = []
    
    var intPagePendingOrders = 1
    var intPageDeliveredOrders = 1
    
    var strSelectedTab = "PENDING"
    
    var strEngineerId = ""
    
    //MARK: - View Life Cycle
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.designView()
        self.loadData()
    }
    
    //MARK: - Initialization Method
    
    func designView() -> Void {
        
        btnSupportRadioGroup[0].isSelected = true
        strSupportType = (btnSupportRadioGroup[0].titleLabel?.text)!
        
        btnReceivedRadioGroup[1].isSelected = true
        strReceivedType = (btnReceivedRadioGroup[1].titleLabel?.text)!
        
        tblViewPending.register(UINib(nibName: "MyOrderPendingCell", bundle: nil), forCellReuseIdentifier: "cell")
        tblViewDelivered.register(UINib(nibName: "MyOrderCell", bundle: nil), forCellReuseIdentifier: "cell")
        tblViewPending.separatorColor = UIColor.clear
        tblViewDelivered.separatorColor = UIColor.clear
        
        tblViewPending.isHidden = true
        tblViewDelivered.isHidden = true
        
        if Defaults.userType() == "ENGINEER"{
            self.showRedeemNow()
        }
    }
    
    func loadData() -> Void {
        
        lblRedeem.text = "NO PENDING ORDERS"
        callShowPendingOrders()
        callShowDeliveredOrders()
    }
    
    //MARK: - Web Service
    
    func callShowPendingOrders() -> Void {
        
        if isServerReachable(){
            
            var dict: [String : Any] = [:]
            dict["the_engineer_id"] = strEngineerId
            dict["page_no"] = intPagePendingOrders
            
            SVProgressHUD.show()
            SSParserLayer.callShowMyPendingOrder(dict, handler: { strStatus, strMessage, dictResponse in
                SVProgressHUD.dismiss()
                if (strStatus == "YES") {
                    self.intPagePendingOrders += 1
                    let json = JSON(dictResponse!)
                    let array = json["order_data"].arrayValue
                    
                    self.arrPendingOrders += array
                    self.tblViewPending.isHidden = self.arrPendingOrders.count == 0 ? true : false
                    self.viewReedem.isHidden = self.arrPendingOrders.count != 0 ? true : false
                    print("Pending:",self.arrPendingOrders)
                    self.tblViewPending.reloadData()
                    
                }else{
                    //self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                    print("No pending orders")
                }
            })
            
        }else{
            //showToastAlert(StringConstant.kNoInternet)
        }
        
    }
    
    func callShowDeliveredOrders() -> Void {
        if isServerReachable(){
            
            var dict: [String : Any] = [:]
            dict["the_engineer_id"] = strEngineerId
            dict["page_no"] = intPageDeliveredOrders
             
            SVProgressHUD.show()
            SSParserLayer.callShowMyDeliveredOrder(dict, handler: { strStatus, strMessage, dictResponse in
                SVProgressHUD.dismiss()
                if (strStatus == "YES") {
                    self.intPageDeliveredOrders += 1
                    let json = JSON(dictResponse!)
                    let array = json["order_data"].arrayValue
                    self.arrDeliveredOrders += array
                    print("Delivered:",self.arrDeliveredOrders)
                    self.tblViewDelivered.reloadData()
                    
                }else{
                    //self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                }
            })
            
        }else{
            showToastAlert(StringConstant.kNoInternet)
        }
    }
    
    //MARK: - IBAction's
    
    @IBAction func btnBackClicked(_ sender: UIBarButtonItem) {
        navigationController?.popViewController(animated: true)
    }
    
    @IBAction func btnTabsClicked(_ sender: UIButton) {
        
        for button in btnTabs {
            button.isSelected = false
        }
        sender.isSelected = true
        
        let array = (sender.titleLabel?.text == "PENDING") ? arrPendingOrders : arrDeliveredOrders
        if array.count == 0 {
            //showToastAlert("No record found")
            tblViewDelivered.isHidden = true
            tblViewPending.isHidden = true
            
            if (sender.titleLabel?.text == "PENDING") {
                lblRedeem.text = "NO PENDING ORDERS"
                if let attributedTitle = btnRedeem.attributedTitle(for: .normal) {
                    let mutableAttributedTitle = NSMutableAttributedString(attributedString: attributedTitle)
                    mutableAttributedTitle.replaceCharacters(in: NSMakeRange(0, mutableAttributedTitle.length), with: "Redeem Now")
                    btnRedeem.setAttributedTitle(mutableAttributedTitle, for: .normal)
                }
                
            }else{
                lblRedeem.text = "NO ORDERS YET"
                if let attributedTitle = btnRedeem.attributedTitle(for: .normal) {
                    let mutableAttributedTitle = NSMutableAttributedString(attributedString: attributedTitle)
                    mutableAttributedTitle.replaceCharacters(in: NSMakeRange(0, mutableAttributedTitle.length), with: "Redeem Your Gift")
                    btnRedeem.setAttributedTitle(mutableAttributedTitle, for: .normal)
                }
            }
            
        }else{
            if (sender.titleLabel?.text == "PENDING") {
                tblViewPending.isHidden = false
            }else{
                tblViewDelivered.isHidden = false
            }
        }
        
        if (sender.titleLabel?.text == "PENDING") {
            viewReedem.isHidden = (arrPendingOrders.count != 0) ? true : false
        } else {
            viewReedem.isHidden = true;
        }
        
        strSelectedTab = (sender.titleLabel?.text)!
        tblViewPending.isHidden = (sender.titleLabel?.text == "PENDING") ? false : true
        
    }
    
    @IBAction func btnSupportRadioClicked(_ sender: UIButton) {
        for button in btnSupportRadioGroup {
            button.isSelected = false
        }
        sender.isSelected = true
        strSupportType = sender.titleLabel?.text ?? ""
    }
    
    @IBAction func btnReceivedRadioClicked(_ sender: UIButton) {
        for button in btnReceivedRadioGroup {
            button.isSelected = false
        }
        sender.isSelected = true
        strReceivedType = sender.titleLabel?.text ?? ""
    }
    
    @IBAction func btnSupportSubmitClicked(_ sender: FPButton) {
        print(strOrderId)
        print(strSupportType)
        
        if strSupportType == "" {
            showToastAlert("Please select any option")
            return
        }
        
        if isServerReachable(){
            //the_engineer_id,order_id,support_type,comment
            var dict: [String : Any] = [:]
            dict["the_engineer_id"] = Defaults.engineerId()
            dict["order_id"] = strOrderId
            dict["support_type"] = strSupportType
            dict["comment"] = txtViewSupport.text
            
            SVProgressHUD.show()
            SSParserLayer.callSubmitSupportWithRespectedOrder(dict, handler: { strStatus, strMessage, dictResponse in
                SVProgressHUD.dismiss()
                if (strStatus == "YES") {
                    self.viewSupport.superview?.removeFromSuperview()
                    if let strMsg = strMessage {
                        self.showToastAlert(strMsg)
                    }
                }else{
                    self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                }
            })
            
        }else{
            showToastAlert(StringConstant.kNoInternet)
        }
        
        
    }
    
    @IBAction func btnRedeemClicked(_ sender: UIButton) {
        performSegue(withIdentifier: "myOrdersToGiftList", sender: self)
    }
    
    @IBAction func btnSaveClicked(_ sender: FPButton) {
        if strReceivedType == "" {
            showToastAlert("Please select any option")
            return
        }
        
        if isServerReachable(){
            //the_engineer_id,order_id,is_order_received
            var dict: [String : Any] = [:]
            dict["the_engineer_id"] = Defaults.engineerId()
            dict["order_id"] = strOrderId
            dict["is_order_received"] = strReceivedType
            
            SVProgressHUD.show()
            SSParserLayer.callGiftReceived(dict, handler: { strStatus, strMessage, dictResponse in
                SVProgressHUD.dismiss()
                if (strStatus == "YES") {
                    self.viewReceived.superview?.removeFromSuperview()
                    if let strMsg = strMessage {
                        self.showToastAlert(strMsg)
                    }
                }else{
                    self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                }
            })
            
        }else{
            showToastAlert(StringConstant.kNoInternet)
        }
    }
    
    //MARK: - Helper Method
    
    func showRedeemNow() -> Void {
        self.view.addSubview(viewReedem)
        viewReedem.center = self.view.center
    }
}

extension MyOrdersViewController : UITableViewDataSource, UITableViewDelegate, UIGestureRecognizerDelegate {
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        if tableView == tblViewPending {
            return arrPendingOrders.count
        }else{
            return arrDeliveredOrders.count
        }
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cellIdentifier = "cell"
        
        
        if tableView == tblViewPending {
            
            let cell = tableView.dequeueReusableCell(withIdentifier: cellIdentifier, for: indexPath) as? MyOrderPendingCell
            
            let dictOrder = arrPendingOrders[indexPath.row].dictionaryValue
            
            cell?.lblProductName.text = dictOrder["gift_title"]?.stringValue
            cell?.lblPointsRequired.text = dictOrder["point_taken_text"]?.stringValue
            cell?.lblOrderId.text = "OrderId : \(dictOrder["order_id"]?.stringValue ?? "")"
            
            if dictOrder["expected_delivery_date"]?.stringValue == "" {
                cell?.lblDeliveryDate.text = "Expected delivery date : To be Updated"
            }else{
                cell?.lblDeliveryDate.text = "Expected delivery date : \(dictOrder["expected_delivery_date"]?.stringValue ?? "")"
            }
            
            cell?.imgViewProduct.sd_setImage(with: URL(string: arrPendingOrders[indexPath.row]["gift_image_url"].stringValue), placeholderImage: UIImage(named: "image_placeholder"))
            
            cell?.btnSupport.isHidden = true
            cell?.btnDeliveryConfirmation.isHidden = true
            
            return cell!
            
        }else{
            
            let cell = tableView.dequeueReusableCell(withIdentifier: cellIdentifier, for: indexPath) as? MyOrderCell
            
            let dictOrder = arrDeliveredOrders[indexPath.row].dictionaryValue
            
            cell?.lblProductName.text = dictOrder["gift_title"]?.stringValue
            cell?.lblPointsRequired.text = dictOrder["point_taken_text"]?.stringValue
            cell?.lblOrderId.text = "OrderId : \(dictOrder["order_id"]?.stringValue ?? "")"
            cell?.lblDeliveryStatus.text = "Status : \(dictOrder["status"]?.stringValue ?? "")"
            cell?.lblDeliveryDate.text = "Delivery date : \(dictOrder["delivery_date"]?.stringValue ?? "")"
            cell?.lblType.text = "Type : \(dictOrder["s_type"]?.stringValue ?? "")"
            cell?.lblComments.text = "Comments : \(dictOrder["s_comment"]?.stringValue ?? "")"
            cell?.imgViewProduct.sd_setImage(with: URL(string: arrDeliveredOrders[indexPath.row]["gift_image_url"].stringValue), placeholderImage: UIImage(named: "image_placeholder"))
            
            cell?.btnSupport.accessibilityLabel = dictOrder["order_id"]?.stringValue ?? ""
            cell?.btnSupport.addTarget(self, action: #selector(btnSupportClicked(_:)), for: UIControl.Event.touchUpInside)
            cell?.btnDeliveryConfirmation.isHidden = (dictOrder["is_order_received"]?.stringValue == "YES") ? true : false
            cell?.btnDeliveryConfirmation.accessibilityLabel = dictOrder["order_id"]?.stringValue ?? ""
            cell?.btnDeliveryConfirmation.addTarget(self, action: #selector(btnDeliveryConfirmationClicked(_:)), for: UIControl.Event.touchUpInside)
            cell?.btnSupport.isHidden = false
            
            if let navController = self.navigationController, navController.viewControllers.count >= 2 {
                let viewController = navController.viewControllers[navController.viewControllers.count - 2]
                if (viewController is EngineerProfileViewController){
                    cell?.btnSupport.isHidden = true
                }
            }
            return cell!
        }
    }
    
    func scrollViewDidEndDragging(_ scrollView: UIScrollView, willDecelerate decelerate: Bool) {
        
        // UITableView only moves in one direction, y axis
        let currentOffset = scrollView.contentOffset.y
        let maximumOffset = scrollView.contentSize.height - scrollView.frame.size.height
        
        // Change 10.0 to adjust the distance from bottom
        if maximumOffset - currentOffset <= 10.0 {
            if scrollView == tblViewPending {
                callShowPendingOrders()
            }else{
                callShowDeliveredOrders()
            }
        }
    }
    
    //MARK: - Cell Action
    
    @objc func btnSupportClicked(_ sender: UIButton){ //<- needs `@objc`
        print("suport clicked.")
        
        strOrderId = sender.accessibilityLabel!
        let viewBase = UIView.init(frame: CGRect(x: 0, y: 0, width: view.frame.size.width, height: view.frame.size.height))
        viewBase.backgroundColor = UIColor.black.withAlphaComponent(0.3)
        viewBase.addSubview(viewSupport)
        viewSupport.center = viewBase.center
        //viewBase.addGestureRecognizer(UITapGestureRecognizer(target: self, action:#selector(self.handleTap(_:))))
        
        let tapGesture = UITapGestureRecognizer(target: self, action: #selector(self.handleTap(_:)))
        tapGesture.delegate = self
        viewBase.addGestureRecognizer(tapGesture)
        
        view.addSubview(viewBase)
        
    }
    
    @objc func btnDeliveryConfirmationClicked(_ sender: UIButton){ //<- needs `@objc`
        print("delivery confirmation clicked.")
        
        strOrderId = sender.accessibilityLabel!
        let viewBase = UIView.init(frame: CGRect(x: 0, y: 0, width: view.frame.size.width, height: view.frame.size.height))
        viewBase.backgroundColor = UIColor.black.withAlphaComponent(0.3)
        viewBase.addSubview(viewReceived)
        viewReceived.center = viewBase.center
        //viewBase.addGestureRecognizer(UITapGestureRecognizer(target: self, action:#selector(self.handleTap(_:))))
        
        let tapGesture = UITapGestureRecognizer(target: self, action: #selector(self.handleTap(_:)))
        tapGesture.delegate = self
        viewBase.addGestureRecognizer(tapGesture)
        
        view.addSubview(viewBase)
        
    }
    
    //MARK: - Gesture
    
    @objc func handleTap(_ sender: UITapGestureRecognizer? = nil) {
        sender?.view?.removeFromSuperview()
    }
    
    func gestureRecognizer(_ gestureRecognizer: UIGestureRecognizer, shouldReceive touch: UITouch) -> Bool {
        return touch.view == gestureRecognizer.view
    }
    
}

extension MyOrdersViewController : UITextViewDelegate {
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
            self.viewSupport.center.y -= 100
        }, completion: { (finished) -> Void in
            
        })
        return true
    }
    
    func textViewShouldEndEditing(_ textView: UITextView) -> Bool{
        UIView.animate(withDuration: 0.25,
                       delay: 0.0,
                       options: UIView.AnimationOptions.curveEaseInOut,
                       animations: { () -> Void in
            self.viewSupport.center.y += 100
        }, completion: { (finished) -> Void in
            
        })
        return true
    }
    
}
