//
//  GiftsViewController.swift
//  StarStellar
//
//  Created by Apple on 02/08/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit
import SwiftyJSON
import SVProgressHUD 
import Alamofire
import SDWebImage

class GiftsViewController: BaseViewController, StellarPointsDelegate {
    
    @IBOutlet weak var rightBarButtonMyPoints: UIBarButtonItem!
    //@IBOutlet weak var lblMyPoints: UILabel!
    @IBOutlet weak var collViewGifts: UICollectionView!
    var arrGifts = [JSON]()
    var intMyStellarPoints = 0
    
    var intGiftPageNo = 1
    var strCategoryId = "" 
    
    //MARK: - View Life Cycle
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.designView()
        self.loadData()
    }
    
    //MARK: - Initialization Method
    
    func designView() -> Void {        
        
        rightBarButtonMyPoints.setTitleTextAttributes([ NSAttributedString.Key.font: UIFont.systemFont(ofSize: 12, weight: UIFont.Weight.semibold)], for: UIControl.State.normal)
        collViewGifts.register(UINib(nibName: "GiftCell", bundle: nil), forCellWithReuseIdentifier: "cell")
    }
    
    func loadData() -> Void {
       callShowMyGifts()
    }
    
    //MARK: -  Web Servide
    
    func callShowMyGifts() -> Void {
        
        if isServerReachable(){
            
            var dict: [String : Any] = [:]
            dict["the_engineer_id"] = Defaults.engineerId()
            dict["page_no"] = intGiftPageNo
            dict["category_id"] = strCategoryId
            
            SVProgressHUD.show()
            SSParserLayer.callShowMyGifts(dict, handler: { strStatus, strMessage, dictResponse in
                SVProgressHUD.dismiss()
                if (strStatus == "YES") {
                    
                    self.intGiftPageNo += 1
                    let json = JSON(dictResponse!)
                    self.intMyStellarPoints = json["e_points"].intValue
                    self.rightBarButtonMyPoints.title = json["e_points_msg"].stringValue
                    //self.lblMyPoints.text = json["e_points_msg"].stringValue
                    //self.lblMyPoints.text = String(format: "My Stellar Points\n%@", self.strMyStellarPoints)
                    self.arrGifts += json["gift_data"].arrayValue
                    self.collViewGifts.reloadData()
                    print(json)
                }else{
                    self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
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
    
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        if segue.identifier == "listToGiftDetails" {
            
            let btnPoints = sender as! UIButton
            print(btnPoints.accessibilityElements!)
            let gdvc = segue.destination as! GiftDetailsViewController
            gdvc.dictGift = btnPoints.accessibilityElements![0] as! JSON
            gdvc.intMyStellarPoints = intMyStellarPoints
            gdvc.delegate = self
            
        }
    }
    
    //MARK: - Stellar Points Delegate
    
    func updateStellarPoints(intStellerPoints: Int) {
        //Stellar Points : 7845
        //lblMyPoints.text = "Stellar Points : \(intStellerPoints)"
        rightBarButtonMyPoints.title = "Stellar Points : \(intStellerPoints)"
        intMyStellarPoints = intStellerPoints
    }
    
}

//MARK: - UICollectionView Delegate and Datasource

extension GiftsViewController : UICollectionViewDataSource, UICollectionViewDelegate , UICollectionViewDelegateFlowLayout{
    func collectionView(_ collectionView: UICollectionView, numberOfItemsInSection section: Int) -> Int {
        return arrGifts.count;
    }
    
    func collectionView(_ collectionView: UICollectionView, cellForItemAt indexPath: IndexPath) -> UICollectionViewCell {
        
        let cellIdentifier = "cell"
        let cell = collectionView.dequeueReusableCell(withReuseIdentifier: cellIdentifier, for: indexPath) as? GiftCell
        let dict = arrGifts[indexPath.row]
        cell?.lblItem.text = dict["gift_title"].stringValue
        
        
        cell?.btnPoints.setTitle(dict["point_require_text"].stringValue, for: UIControl.State.normal)
        
        cell?.btnPoints.backgroundColor = dict["button_status"].stringValue == "ENABLE" ? UIColor.white : UIColor.lightGray
        cell?.btnPoints.setTitleColor(dict["button_status"].stringValue == "ENABLE" ? UIColor.black : UIColor.darkGray, for: UIControl.State.normal)
        cell?.btnPoints.addTarget(self, action: #selector(self.btnPointsClicked(_:)), for: UIControl.Event.touchUpInside)
        cell?.btnPoints.isUserInteractionEnabled = dict["button_status"].stringValue == "ENABLE" ? true : false
        cell?.btnPoints.accessibilityElements = [dict]
        
        cell?.btnInfo.accessibilityLabel = dict["gift_description"].stringValue
        cell?.btnInfo.addTarget(self, action: #selector(self.btnInfoClicked(_:)), for: UIControl.Event.touchUpInside)
        cell?.imgViewGifts.sd_setImage(with: URL(string: dict["gift_image_url"].stringValue), placeholderImage: UIImage(named: "image_placeholder"))
        
        
//        request(dict["gift_image_url"].stringValue, method: .get)
//            .validate()
//            .responseData(completionHandler: { (responseData) in
//                cell?.imgViewGifts.image = UIImage(data: responseData.data!)
//            })
        return cell!
    }
    
    func collectionView(_ collectionView: UICollectionView,
                        layout collectionViewLayout: UICollectionViewLayout,
                        sizeForItemAt indexPath: IndexPath) -> CGSize {
        
        let size = CGSize(width: ((UIScreen.main.bounds).size.width - 15) / 2, height: 170)
        return size
        
    }
    
    func scrollViewDidEndDragging(_ scrollView: UIScrollView, willDecelerate decelerate: Bool) {
        
        // UITableView only moves in one direction, y axis
        let currentOffset = scrollView.contentOffset.y
        let maximumOffset = scrollView.contentSize.height - scrollView.frame.size.height
        
        // Change 10.0 to adjust the distance from bottom
        if maximumOffset - currentOffset <= 10.0 {
            print("load more data")
            callShowMyGifts()
        }
    }
    
    //MARK: - Cell Action
    
    @objc func btnInfoClicked(_ sender: UIButton){ //<- needs `@objc`
        showAlert(sender.accessibilityLabel ?? "")
    }
    
    @objc func btnPointsClicked(_ sender: UIButton){ //<- needs `@objc`
        performSegue(withIdentifier: "listToGiftDetails", sender: sender)
    }
    
}
