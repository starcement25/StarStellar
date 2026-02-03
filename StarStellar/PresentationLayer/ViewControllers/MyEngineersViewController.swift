//
//  MyEngineersViewController.swift
//  StarStellar
//
//  Created by Apple on 22/08/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit
import SwiftyJSON
import SVProgressHUD
import Alamofire
import SDWebImage

class MyEngineersViewController: BaseViewController {
    
    @IBOutlet weak var tblViewEngineers: UITableView!
    //var strSearchTerm : String = ""
    var strStatus : String = ""
    var intPageNo = 1
    var arrMyEngineers : [JSON] = []
    var arrMyEngineersTemp : [JSON] = []
    var arrPendingEngineer : [JSON] = []
    @IBOutlet weak var btnEngineerToBeApproved: UIButton!
    @IBOutlet weak var searchBarEngineer: UISearchBar!
    
    
    //MARK: - View Life Cycle
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.designView()
        self.loadData()
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        
        intPageNo = 1
        arrMyEngineers = []
        arrMyEngineersTemp = []
        arrPendingEngineer = []
        
        callMyEngineers(searchBarEngineer.text ?? "")
        callShowPendingEngineer()
    }
    
    //MARK: - Initialization Method
    
    func designView() -> Void {
        tblViewEngineers.register(UINib(nibName: "MyEngineersCell", bundle: nil), forCellReuseIdentifier: "cell")
        tblViewEngineers.separatorColor = .clear
    }
    
    func loadData() -> Void {
        
    }
    
    //MARK: - Web Service
    
    func callMyEngineers(_ strSearchTerm : String) -> Void {
        if isServerReachable() {
            
            //te_code,page_no,search_term,status
            
            var dict: [String : Any] = [:]
            dict["te_code"]   = Defaults.teCode()
            dict["page_no"] = intPageNo
            dict["search_term"]  = strSearchTerm
            dict["status"]  = strStatus
            
            SVProgressHUD.show()
            SSParserLayer.callShowMappedEngineersByTE(dict) { [self] (strStatus, strMessage, dictResponse) in
                SVProgressHUD.dismiss()
                if strStatus == "YES" {
                    let json = JSON(dictResponse!)
                    let array = json["engineer_data"].arrayValue
                    intPageNo += 1
                    self.arrMyEngineers += array
                    self.arrMyEngineersTemp += array
                    self.tblViewEngineers.reloadData()
                    
                }else{
                    self.tblViewEngineers.reloadData()
                    self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                }
            }
            
        }else{
            showToastAlert(StringConstant.kNoInternet)
        }
    }
    
    
    func callShowPendingEngineer() -> Void {
        if isServerReachable() {
            
            var dict: [String : Any] = [:]
            dict["te_code"]   = Defaults.teCode()
            
            SVProgressHUD.show()
            SSParserLayer.callShowPendingEngineersByTE(dict) { (strStatus, strMessage, dictResponse) in
                SVProgressHUD.dismiss()
                if strStatus == "YES" {
                    
                    let json = JSON(dictResponse!)
                    self.arrPendingEngineer = json["engineer_data"].arrayValue
                    self.btnEngineerToBeApproved.setTitle("\(self.arrPendingEngineer.count) ENGINERRS TO BE APPROVED", for: UIControl.State.normal)
                }else{
                    self.btnEngineerToBeApproved.setTitle("0 ENGINERRS TO BE APPROVED", for: UIControl.State.normal)
                    //self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                }
            }
        }else{
            showToastAlert(StringConstant.kNoInternet)
        }
    }
    
    //MARK: - IBAction's
    
    @IBAction func btnBackClicked(_ sender: UIBarButtonItem) {
        navigationController?.popViewController(animated: true)
    }
    
    @IBAction func btnFilterClicked(_ sender: UIBarButtonItem) {
        
        let actionSheet = UIAlertController(title: "Filter by:", message: nil, preferredStyle: UIAlertController.Style.actionSheet)
        actionSheet.addAction(UIAlertAction(title: "All", style: UIAlertAction.Style.default, handler: { (action) in
            self.arrMyEngineers.removeAll()
            self.arrMyEngineers = self.arrMyEngineersTemp
            self.tblViewEngineers.reloadData()
        }))
        
        actionSheet.addAction(UIAlertAction(title: "Active", style: UIAlertAction.Style.default, handler: { (action) in
            let strStatus = "ACTIVE"
            self.arrMyEngineers.removeAll()
            for engineer in self.arrMyEngineersTemp {
                if engineer["e_status"].stringValue == strStatus {
                    self.arrMyEngineers.append(engineer)
                }
            }
            if self.arrMyEngineers.count == 0 {
                self.showToastAlert("No records found.")
            }
            self.tblViewEngineers.reloadData()
        }))
        
        actionSheet.addAction(UIAlertAction(title: "Inactive", style: UIAlertAction.Style.default, handler: { (action) in
            let strStatus = "INACTIVE"
            self.arrMyEngineers.removeAll()
            for engineer in self.arrMyEngineersTemp {
                if engineer["e_status"].stringValue == strStatus {
                    self.arrMyEngineers.append(engineer)
                }
            }
            if self.arrMyEngineers.count == 0 {
                self.showToastAlert("No records found.")
            }
            self.tblViewEngineers.reloadData()
        }))
        
        actionSheet.addAction(UIAlertAction(title: "Semi active", style: UIAlertAction.Style.default, handler: { (action) in
            let strStatus = "SEMI_ACTIVE"
            self.arrMyEngineers.removeAll()
            for engineer in self.arrMyEngineersTemp {
                if engineer["e_status"].stringValue == strStatus {
                    self.arrMyEngineers.append(engineer)
                }
            }
            if self.arrMyEngineers.count == 0 {
                self.showToastAlert("No records found.")
            }
            self.tblViewEngineers.reloadData()
        }))
        
        actionSheet.addAction(UIAlertAction(title: "CANCEL", style: UIAlertAction.Style.cancel, handler: nil))
        present(actionSheet, animated: true, completion: nil)
        
    }
    
    @IBAction func btnEngineerToBeApprovedClicked(_ sender: UIButton) {
        performSegue(withIdentifier: "mappedEngineerToPendingEngineer", sender: self)
    }
    
    //MARK: - Segue
    
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        if segue.identifier == "engineerListToProfile" {
            if let indexPath = tblViewEngineers.indexPathForSelectedRow{
                let selectedRow = indexPath.row
                let epvc = segue.destination as! EngineerProfileViewController
                epvc.dictProfile = arrMyEngineers[selectedRow]
            }
        }else if segue.identifier == "mappedEngineerToPendingEngineer" {
            print("Send data to pending engineer view controller")
            let tepevc = segue.destination as! TEPendingEngineerVC
            tepevc.arrPendingEngineer = arrPendingEngineer
        }
    }
    
    //MARK: - Helper Method
    
    func getStatusColor(strStatus : String) -> UIColor {
        if strStatus == "ACTIVE" {
            return UIColor.green
        }else if strStatus == "INACTIVE" {
            return UIColor.white
        }else {
            return UIColor.orange
        }
    }
    
}

extension MyEngineersViewController : UITableViewDelegate, UITableViewDataSource {
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrMyEngineers.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cellIdentifier = "cell"
        let cell = tblViewEngineers.dequeueReusableCell(withIdentifier: cellIdentifier, for: indexPath) as? MyEngineersCell
        
        let dictEngineers = arrMyEngineers[indexPath.row].dictionaryValue
        
        cell?.imgViewEngineers.sd_setImage(with: URL(string: dictEngineers["e_profile_image_url"]!.stringValue), placeholderImage: UIImage(named: "user_placeholder"))
        
        cell?.lblName.text = dictEngineers["e_name"]?.stringValue
        cell?.lblLocation.text = dictEngineers["e_city_town"]?.stringValue
        cell?.viewStatus.backgroundColor = getStatusColor(strStatus: (dictEngineers["e_status"]?.stringValue)!)
        return cell!
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        performSegue(withIdentifier: "engineerListToProfile", sender: self)
    }
    
    func scrollViewWillEndDragging(_ scrollView: UIScrollView, withVelocity velocity: CGPoint, targetContentOffset: UnsafeMutablePointer<CGPoint>) {
        view.endEditing(true)
    }
    
    func scrollViewDidEndDragging(_ scrollView: UIScrollView, willDecelerate decelerate: Bool) {
        let currentOffset = scrollView.contentOffset.y
        let maximumOffset = scrollView.contentSize.height - scrollView.frame.size.height
        if maximumOffset - currentOffset <= 10.0 {
            callMyEngineers(searchBarEngineer.text ?? "")
        }
    }
}

extension MyEngineersViewController : UISearchBarDelegate {
    
    func searchBar(_ searchBar: UISearchBar, textDidChange searchText: String) {
        
        /*arrMyEngineers.removeAll()
         for engineer in arrMyEngineersTemp {
         if engineer["e_name"].stringValue.lowercased().contains(searchText.lowercased()) {
         arrMyEngineers.append(engineer)
         }
         }
         if searchText.count == 0 {
         arrMyEngineers = arrMyEngineersTemp
         }
         tblViewEngineers.reloadData()*/
        intPageNo = 1
        arrMyEngineers.removeAll()
        arrMyEngineersTemp.removeAll()
        callMyEngineers(searchText)
    }
    
    func searchBarSearchButtonClicked(_ searchBar: UISearchBar) {
        view.endEditing(true)
    }
    
}
