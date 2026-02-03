//
//  NotificationDetailsViewController.swift
//  StarStellar
//
//  Created by Apple on 30/08/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit
import SDWebImage
import SwiftyJSON

class NotificationDetailsViewController: BaseViewController {

    
    @IBOutlet weak var imgView: UIImageView!
    @IBOutlet weak var lblTitle: UILabel!
    @IBOutlet weak var lblSubtitle: UILabel!
    var dictNotification : JSON = []
    //MARK: - View Life Cycle
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.designView()
        self.loadData()
    }
    
    //MARK: - Initialization Method
    
    func designView() -> Void {
        
    }
    
    func loadData() -> Void {
        imgView.sd_setImage(with: URL(string: dictNotification["m_image_link"].stringValue), placeholderImage: UIImage(named: "image_placeholder"))
        lblTitle.text = dictNotification["m_title"].stringValue
        lblSubtitle.text = dictNotification["m_message"].stringValue
    }
    
    //MARK: - IBAction's
    
    @IBAction func btnBackClicked(_ sender: UIBarButtonItem) {
        navigationController?.popViewController(animated: true)
    }
    
    

}
