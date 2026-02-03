//
//  UpdateLiftingCell.swift
//  StarStellar
//
//  Created by Sanjeet Kumar on 21/09/22.
//  Copyright © 2022 Apple. All rights reserved.
//

import UIKit

class UpdateLiftingCell: UITableViewCell {

    @IBOutlet weak var imgView: FPImageView!
    @IBOutlet weak var lblCompanyName: UILabel!
    @IBOutlet weak var lblMobileNumber: UILabel!
    @IBOutlet weak var lblPlace: UILabel!
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }
    
}
