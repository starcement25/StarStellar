//
//  ProfileCell.swift
//  StarStellar
//
//  Created by Apple on 23/07/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit

class ProfileCell: UITableViewCell {

    @IBOutlet weak var lblItem: UILabel!
    @IBOutlet weak var lblDetails: UILabel!
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }
    
}
