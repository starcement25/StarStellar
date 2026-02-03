//
//  MyOrderCell.swift
//  StarStellar
//
//  Created by Apple on 07/08/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit

class MyOrderCell: UITableViewCell {

    @IBOutlet weak var imgViewProduct: UIImageView!
    @IBOutlet weak var lblProductName: UILabel!
    @IBOutlet weak var lblPointsRequired: UILabel!
    @IBOutlet weak var lblOrderId: UILabel!
    @IBOutlet weak var lblDeliveryDate: UILabel!
    @IBOutlet weak var lblDeliveryStatus: UILabel!
    @IBOutlet weak var btnSupport: UIButton!
    @IBOutlet weak var btnDeliveryConfirmation: UIButton!
    @IBOutlet weak var lblType: UILabel!
    @IBOutlet weak var lblComments: UILabel!
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }
    
}
