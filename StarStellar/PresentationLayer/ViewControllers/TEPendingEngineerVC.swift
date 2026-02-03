//
//  TEPendingEngineerVC.swift
//  StarStellar
//
//  Created by Apple on 15/11/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit
import SwiftyJSON
import SVProgressHUD

class TEPendingEngineerVC: BaseViewController {
    
    @IBOutlet weak var tblViewEngineer: UITableView!
    var arrPendingEngineer : [JSON] = []
    var arrBranchList : [JSON] = []
    
    //MARK: - View Life Cycle
    override func viewDidLoad() {
        super.viewDidLoad()
        designView()
        loadData()
        // Do any additional setup after loading the view.
    }
    
    //MARK: Initialization Method
    
    func designView() -> Void {
        tblViewEngineer.register(UINib(nibName: "TEPendingEngineerCell", bundle: nil), forCellReuseIdentifier: "cell")
    }
    
    func loadData() -> Void {
        print(arrPendingEngineer)
    }
    
    //MARK: - IBAction's
    
    @IBAction func btnBackClicked(_ sender: UIBarButtonItem) {
        navigationController?.popViewController(animated: true)
    }
    
    //MARK: - Web Service
    
    func callApproveRejectEngineerByTE(_ eid : String, _ status : String,_ branchCode : String) -> Void {
        
        if isServerReachable(){
            
            //            te_code,eid,status
            //
            //            Note: All fields are mandatory.
            //            status = APPROVE or REJECT
            
            var dict: [String : Any] = [:]
            dict["te_code"] = Defaults.teCode()
            dict["eid"] = eid
            dict["status"] = status
            dict["selected_branch"] = branchCode
            
            SVProgressHUD.show()
            SSParserLayer.callApproveRejectEngineerByTE(dict, handler: { strStatus, strMessage, dictResponse in
                SVProgressHUD.dismiss()
                if (strStatus == "YES") {
                    let json = JSON(dictResponse!)
                    print(json)
                    self.navigationController?.popViewController(animated: true)
                }else{
                    self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                }
            })
        }else{
            showToastAlert(StringConstant.kNoInternet)
        }
    }
    
    func callBranchZoneListForTE(_ strEngineerId : String,_ status : String) -> Void {
        
        if isServerReachable(){
            
            var dict: [String : Any] = [:]
            dict["te_code"] = Defaults.teCode()
            
            SVProgressHUD.show()
            SSParserLayer.callBranchZoneListForTE(dict, handler: { [self] strStatus, strMessage, dictResponse in
                SVProgressHUD.dismiss()
                if (strStatus == "YES") {
                    let json = JSON(dictResponse!)
                    print(json)
                    arrBranchList = json["branch_code"].arrayValue
                    let alert = UIAlertController(title: "Please select the branch", message: nil, preferredStyle: .actionSheet)
                    
                    for (index, dictBranch) in arrBranchList.enumerated() {
                        
                      print("Item \(index): \(dictBranch)")
                        let action = UIAlertAction(title: dictBranch["br_name"].stringValue, style: .default) { [self] alertAction in
                            print(alertAction.accessibilityValue ?? "")
                            let index = "\(alertAction.accessibilityValue ?? "")"
                            callApproveRejectEngineerByTE(strEngineerId, status, arrBranchList[Int(index) ?? 0]["br_cod"].stringValue)
                        }
                        action.accessibilityValue = "\(index)"
                        alert.addAction(action)
                    }
                    
                    alert.addAction(UIAlertAction(title: "Cancel", style: .cancel))
                    present(alert, animated: true, completion: nil)
                }else{
                    self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                }
            })
        }else{
            showToastAlert(StringConstant.kNoInternet)
        }
    }
}

//MARK: - UITableView Delegate and DataSource

extension TEPendingEngineerVC : UITableViewDelegate, UITableViewDataSource {
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrPendingEngineer.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cellIdentifier = "cell"
        let cell = tblViewEngineer.dequeueReusableCell(withIdentifier: cellIdentifier, for: indexPath) as! TEPendingEngineerCell
        cell.lblEngineerName.text = arrPendingEngineer[indexPath.row]["e_name"].stringValue
        cell.strEngineerId = arrPendingEngineer[indexPath.row]["eid"].stringValue
        cell.lblEngineerMobile.text = "Mobile : \(arrPendingEngineer[indexPath.row]["e_name"].stringValue)"
        cell.btnApprove.addTarget(self, action: #selector(btnApproveClicked(_:)), for: UIControl.Event.touchUpInside)
        cell.btnReject.addTarget(self, action: #selector(btnRejectClicked(_:)), for: UIControl.Event.touchUpInside)
        return cell
    }
    
    //MARK: - Cell Button Action
    
    @objc func btnApproveClicked(_ sender : UIButton) {
        print("Approve")
        let cell = sender.superview?.superview as? TEPendingEngineerCell
        //callApproveRejectEngineerByTE(cell!.strEngineerId, "APPROVE")
        callBranchZoneListForTE(cell!.strEngineerId, "APPROVE")
    }
    
    @objc func btnRejectClicked(_ sender : UIButton) {
        print("Reject")
        
        let alert = UIAlertController(title: nil, message: "Do you want to reject?", preferredStyle: UIAlertController.Style.alert)
        
        alert.addAction(UIAlertAction(title: "YES", style: UIAlertAction.Style.default, handler: { (UIAlertAction) in
            let cell = sender.superview?.superview as? TEPendingEngineerCell
            //self.callApproveRejectEngineerByTE(cell!.strEngineerId, "REJECT")
            self.callApproveRejectEngineerByTE(cell!.strEngineerId, "REJECT", "")
        }))
        
        alert.addAction(UIAlertAction(title: "NO", style: UIAlertAction.Style.default, handler: { (UIAlertAction) in
            
        }))
        
        present(alert, animated: true, completion: nil)
    }
    
}

