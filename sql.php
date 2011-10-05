<?php

function imageFolder()
{
	return '\/Images\/images_agents1\/';
}

function getSqlForProfiles()
{
    return "select
	    (select top 1 ReferralCode.referralCode from [Order] 
	    	left outer join ReferralCode on [Order].intReferralCodeID=ReferralCode.intReferralCodeID
	    	where [Order].intMemberid=Profiles.intOwnerID order by dteDate desc) as referral_code,
	IntroTitle as title,
	MarketingMsg as description,
	ProfileEmail as email,
	DateCreated as created_at,
	(select top 1 Path from  LogosAndImages where OwnerID=Profiles.intOwnerID) as banner,
	(select top 1 Path from  LogosAndImages where OwnerID=Profiles.intProfileID) as profile_photo,
	Company.URL as website, Company.CompanyID as legacy_company_id, Company.CompanyName as trading_name
	from Profiles
	left outer join Member on Member.MemberID=Profiles.intOwnerID
	inner join Company on Member.MemberID=Company.MemberID
	order by intProfileId";
}

function getSqlForPhotos($pid)
{
    return sprintf ("
        select
			vcDescription as caption,
			vcFilePathName as original_url,
			vcThumbnailPath as thumbnail_url,
			vcDisplaySortOrder as [order],
			bitMainImage as [default],
			CONVERT(varchar,modifiedDate,126) as updated_at
		from PropertyImages
		where intPropertyID=%d", $pid);
}

function getSqlForCompany($pid)
{
	$s = imageFolder();

    return sprintf ("select
		person.FirstName as first_name,
		person.Surname as last_name,
		person.Email as email_address,
		comp.Telephone_No as phone_number,
		comp.FeedReferenceID as feed_ref,
		comp.CompanyID as legacy_company_id,
		person.Mobile as mobile_number,
		person.TradingName as trading_name,
		addr.vcStreetNo as street_address,
		addr.vcStreetName as street_address_1,
		addr.vcSuburb as suburb,
		addr.intPostCode as postcode,
		addr.chrState as state,
		'/Images/images_agents1/' + banners.Path as banner
		from dbo.Property as prop
		join dbo.Person as person on person.MemberID=prop.intOwnerID
		left outer join dbo.PersonContact as contact on contact.PersonId=person.PersonID
		join dbo.Company as comp on comp.MemberId=person.MemberID
		join dbo.LogosAndImages as banners on banners.OwnerID=comp.CompanyID
		left outer join dbo.Address as addr on addr.intAddressID=person.AddressID
		where prop.intPropertyID=%d", $pid);
}

?>
