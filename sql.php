<?php

function imageFolder()
{
	return '/Images/images_agents1/';
}

function getSqlForProfiles()
{
	return "select
		Company.CompanyID as legacy_company_id,
		Company.URL as website,
		Company.LicenseNo as trading_name,
		Company.AdvertisingByline as byline,
		(select top 1 ReferralCode.referralCode from [Order]
		left outer join ReferralCode on [Order].intReferralCodeID=ReferralCode.intReferralCodeID
		where [Order].intMemberid=Profiles.intOwnerID order by dteDate desc) as referral_code,
		IntroTitle as profile_title,
		CONVERT(TEXT,MarketingMsg) as profile_description,
		(select top 1 Path from LogosAndImages where OwnerID=Profiles.intProfileID) as profile_photo_url
		from Profiles
		join Company on Company.CompanyID=Profiles.intOwnerID
		where (select top 1 ReferralCode.referralCode from [Order]
		left outer join ReferralCode on [Order].intReferralCodeID=ReferralCode.intReferralCodeID
		where [Order].intMemberid=Profiles.intOwnerID order by dteDate desc) is not null
		order by Company.CompanyID";

//return "select
//			Company.CompanyID as legacy_company_id,
//			Company.URL as website,
//			Company.LicenseNo as trading_name,
//			Company.AdvertisingByline as byline,
//	    (select top 1 ReferralCode.referralCode from [Order] 
//	    	left outer join ReferralCode on [Order].intReferralCodeID=ReferralCode.intReferralCodeID
//	    	where [Order].intMemberid=Profiles.intOwnerID order by dteDate desc) as referral_code,
//	IntroTitle as profile_title,
//	MarketingMsg as profile_description,
//	(select top 1 Path from LogosAndImages where OwnerID=Profiles.intProfileID) as profile_photo_url
//	from Profiles
//	left outer join Member on Member.MemberID=Profiles.intOwnerID
//	inner join Company on Member.MemberID=Company.MemberID
//	order by Company.CompanyID";
}

function getSqlForPropertyManagers($companyId)
{
		return sprintf ("select
			Person.Email as email_address,
			Person.FirstName as first_name,
			Person.Surname as last_name, 
			Person.Fax as fax, 
			Person.Mobile as mobile_phone, 
			Person.BusPhone as phone_number, 
			Person.Visible as visible,
			CAST(CASE WHEN Member.ContactImageId IS NULL THEN NULL ELSE  '/Images/images_agents1/' + CAST(Member.ContactImageId AS varchar(250)) END AS varchar(500)) AS photo,
		 	Company.CompanyID as legacy_company_id from CompanyContact
			join Person on Person.PersonID=CompanyContact.PersonID
			join Company on Company.CompanyID=CompanyContact.CompanyID
			join Member on Member.MemberID=Person.MemberID
			where company.CompanyID=%d", $companyId);
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
