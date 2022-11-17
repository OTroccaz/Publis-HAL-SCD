<?php
header('Content-type: text/html; charset=UTF-8');

//Nettoyage URL
$redir = "non";
$root = 'http';
if (isset ($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")	{
  $root.= "s";
}
if (!isset($_SERVER['REQUEST_URI']) && isset($_SERVER['SCRIPT_NAME']) && isset($_SERVER['QUERY_STRING'])) {
    $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'];
}
$urlnet = $root."://".$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
$urlnet = str_replace(" ", "%20", $urlnet);
while (stripos($urlnet, "%3C") !== false) {
  $redir = "oui";
  $posi = stripos($urlnet, "%3C");
	if (stripos($urlnet, "%3E", $posi) !== false) {
		$posf = stripos($urlnet, "%3E", $posi) + 3;
	}else{
		$posf = $posi + 13;//<a class=
	}
  $urlnet = substr($urlnet, 0, $posi).substr($urlnet, $posf, strlen($urlnet));
}
if ($redir == "oui") {header("Location: ".$urlnet);}
function objectToArray($object) {
  if (!is_object( $object) && !is_array($object)) {
    return $object;
  }
  if (is_object($object)) {
    $object = get_object_vars($object);
  }
  return array_map('objectToArray', $object);
}
function suppression($dossier, $age) {
  $repertoire = opendir($dossier);
    while(false !== ($fichier = readdir($repertoire)))
    {
      $chemin = $dossier."/".$fichier;
      $age_fichier = time() - filemtime($chemin);
      if($fichier != "." && $fichier != ".." && !is_dir($fichier) && $age_fichier > $age)
      {
      unlink($chemin);
      //echo $chemin." - ".date ("F d Y H:i:s.", filemtime($chemin))."<br>";
      }
    }
  closedir($repertoire);
}

function antixss($input) {
  return htmlspecialchars(strip_tags($input), ENT_QUOTES);
}

function mise_en_evidence($phrase, $string, $deb, $fin) {
  $non_letter_chars = '/[^\pL]/iu';
  $words = preg_split($non_letter_chars, $phrase);

  $search_words = array();
  foreach ($words as $word) {
    if (strlen($word) > 2 && !preg_match($non_letter_chars, $word)) {
      $search_words[] = $word;
    }
  }

  $search_words = array_unique($search_words);

  $patterns = array(
    /* à répéter pour chaque caractère accentué possible */
    '/(ae|æ)/iu' => '(ae|æ)',
    '/(oe|œ)/iu' => '(oe|œ)',
    '/[aàáâãäåăãąā]/iu' => '[aàáâãäåăãąā]',
		'/[bḃбБ]/iu' => '[bḃбБ]',
    '/[cçčćĉċцЦ]/iu' => '[cçčćĉċцЦ]',
		'/[dďḋđдД]/iu' => '[dďḋđдД]',
    '/[eèéêëĕěėęēэЭ]/iu' => '[eèéêëĕěėęēэЭ]',
		'/[fḟƒфФ]/iu' => '[fḟƒфФ]',
		'/[gğĝġģгГ]/iu' => '[gğĝġģгГ]',
		'/[hĥħ]/iu' => '[hĥħ]',
    '/[iìíîïĩįīiiиИ]/iu' => '[iìíîïĩįīiiиИ]',
		'/[jĵйЙ]/iu' => '[jĵйЙ]',
		'/[kķк]/iu' => '[kķк]',
		'/[lĺľļłлЛ]/iu' => '[lĺľļłлЛ]',
		'/[mṁм]/iu' => '[mṁм]',
    '/[nñńňņн]/iu' => '[nñńňņн]',
    '/[oòóôõöőøōơ]/iu' => '[oòóôõöőøōơ]',
		'/[pṗпП]/iu' => '[pṗпП]',
		'/[rŕřŗ]/iu' => '[rŕřŗ]',
    '/[sšśŝṡşș]/iu' => '[sšśŝṡşș]',
		'/[tťṫţțŧт]/iu' => '[tťṫţțŧт]',
    '/[uùúûüŭųūư]/iu' => '[uùúûüŭųūư]',
		'/[vв]/iu' => '[vв]',
    '/[wẃẁŵẅ]/iu' => '[wẃẁŵẅ]',
		'/[yýÿỳŷ]/iu' => '[yýÿỳŷ]',
    '/[zžźżзЗ]/iu' => '[zžźżзЗ]',
  );

  foreach ($search_words as $word) {
    $search = preg_quote($word);
    $search = preg_replace(array_keys($patterns), $patterns, $search);
    return preg_replace('/\b' . $search . '(e?s)?\b/iu', $deb.'$0'.$fin, $string);
  }
}

function wd_remove_accents($str, $charset='utf-8')
{
    $str = htmlentities($str, ENT_NOQUOTES, $charset);

    $str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
    $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères

    return $str;
}
function mb_ucwords($str) {
  $str = mb_convert_case($str, MB_CASE_TITLE, "UTF-8");
  return ($str);
}
function prenomCompInit($prenom) {
  $prenom = str_replace("  ", " ",$prenom);
  if (strpos(trim($prenom),"-") !== false) {//Le prénom comporte un tiret
    $postiret = mb_strpos(trim($prenom),'-', 0, 'UTF-8');
    $prenomg = trim(mb_substr($prenom,0,($postiret-1),'UTF-8'));
    $prenomd = trim(mb_substr($prenom,($postiret+1),strlen($prenom),'UTF-8'));
    $autg = mb_substr($prenomg,0,1,'UTF-8');
    $autd = mb_substr($prenomd,0,1,'UTF-8');
    $prenom = mb_ucwords($autg).".-".mb_ucwords($autd).".";
  }else{
    if (strpos(trim($prenom)," ") !== false) {//plusieurs prénoms
      $posespace = strpos(trim($prenom)," ");
      $tabprenom = explode(" ", trim($prenom));
      $p = 0;
      $prenom = "";
      while (isset($tabprenom[$p])) {
        if ($p == 0) {
          $prenom .= mb_ucwords(mb_substr($tabprenom[$p], 0, 1, 'UTF-8')).".";
        }else{
          $prenom .= " ".mb_ucwords(mb_substr($tabprenom[$p], 0, 1, 'UTF-8')).".";
        }
        $p++;
      }
    }else{
      $prenom = mb_ucwords(mb_substr($prenom, 0, 1, 'UTF-8')).".";
    }
  }
  return $prenom;
}
function prenomCompEntier($prenom) {
  $prenom = trim($prenom);
  if (strpos($prenom,"-") !== false) {//Le prénom comporte un tiret
    $postiret = strpos($prenom,"-");
    $autg = substr($prenom,0,$postiret);
    $autd = substr($prenom,($postiret+1),strlen($prenom));
    $prenom = mb_ucwords($autg)."-".mb_ucwords($autd);
  }else{
    $prenom = mb_ucwords($prenom);
  }
  return $prenom;
}

function nomCompEntier($nom) {
  $nom = trim(mb_strtolower($nom,'UTF-8'));
  if (strpos($nom,"-") !== false) {//Le nom comporte un tiret
    $postiret = strpos($nom,"-");
    $autg = substr($nom,0,$postiret);
    $autd = substr($nom,($postiret+1),strlen($nom));
    $nom = mb_ucwords($autg)."-".mb_ucwords($autd);
  }else{
    $nom = mb_ucwords($nom);
  }
  return $nom;
}

function varAut($autvar) {
	$tabaut = explode('~', $autvar);
	$preaut = $tabaut[0];
	$nomaut = $tabaut[1];
	//auteur_exp=soizic chevance,s chevance,s. chevance,sm chevance,s.m. chevance
	$atester = "(";

	$atester .= "authFullName_t:\"".$preaut." ".$nomaut."\"%20OR%20";
	$atester .= "authFullName_t:\"".str_replace(".", "", prenomCompInit($preaut))." ".$nomaut."\"%20OR%20";
	$atester .= "authFullName_t:\"".prenomCompInit($preaut)." ".$nomaut."\"%20OR%20";
	//Si présence d'espaces dans le nom, tester aussi en les remplaçant par des tirets
	if (strpos($nomaut, " ") !== false) {
		$atester .= "authFullName_t:\"".$preaut." ".str_replace(" ", "-", $nomaut)."\"%20OR%20";
		$atester .= "authFullName_t:\"".str_replace(".", "", prenomCompInit($preaut))." ".str_replace(" ", "-", $nomaut)."\"%20OR%20";
		$atester .= "authFullName_t:\"".prenomCompInit($preaut)." ".str_replace(" ", "-", $nomaut)."\"%20OR%20";
	}
	//Si présence de tirets dans le nom, tester aussi en les remplaçant par des espaces
	if (strpos($nomaut, "-") !== false) {
		$atester .= "authFullName_t:\"".$preaut." ".ucwords(str_replace("-", " ", $nomaut))."\"%20OR%20";
		$atester .= "authFullName_t:\"".str_replace(".", "", prenomCompInit($preaut))." ".ucwords(str_replace("-", " ", $nomaut))."\"%20OR%20";
		$atester .= "authFullName_t:\"".prenomCompInit($preaut)." ".ucwords(str_replace("-", " ", $nomaut))."\"%20OR%20";
	}

	//Réitérer les tests avec prénoms 'nettoyés' des caractères accentués
	$preautnet = wd_remove_accents($preaut);

	$atester .= "authFullName_t:\"".$preautnet." ".$nomaut."\"%20OR%20";
	$atester .= "authFullName_t:\"".str_replace(".", "", prenomCompInit($preautnet))." ".$nomaut."\"%20OR%20";
	$atester .= "authFullName_t:\"".prenomCompInit($preautnet)." ".$nomaut."\"%20OR%20";
	//Si présence d'espaces dans le nom, tester aussi en les remplaçant par des tirets
	if (strpos($nomaut, " ") !== false) {
		$atester .= "authFullName_t:\"".$preautnet." ".str_replace(" ", "-", $nomaut)."\"%20OR%20";
		$atester .= "authFullName_t:\"".str_replace(".", "", prenomCompInit($preautnet))." ".str_replace(" ", "-", $nomaut)."\"%20OR%20";
		$atester .= "authFullName_t:\"".prenomCompInit($preautnet)." ".str_replace(" ", "-", $nomaut)."\"%20OR%20";
	}
	//Si présence de tirets dans le nom, tester aussi en les remplaçant par des espaces
	if (strpos($nomaut, "-") !== false) {
		$atester .= "authFullName_t:\"".$preautnet." ".ucwords(str_replace("-", " ", $nomaut))."\"%20OR%20";
		$atester .= "authFullName_t:\"".str_replace(".", "", prenomCompInit($preautnet))." ".ucwords(str_replace("-", " ", $nomaut))."\"%20OR%20";
		$atester .= "authFullName_t:\"".prenomCompInit($preautnet)." ".ucwords(str_replace("-", " ", $nomaut))."\"%20OR%20";
	}

	//Réitérer les tests avec nom 'nettoyé' des caractères accentués
	$nomautnet = wd_remove_accents($nomaut);

	$atester .= "authFullName_t:\"".$preaut." ".$nomautnet."\"%20OR%20";
	$atester .= "authFullName_t:\"".str_replace(".", "", prenomCompInit($preaut))." ".$nomautnet."\"%20OR%20";
	$atester .= "authFullName_t:\"".prenomCompInit($preaut)." ".$nomautnet."\"%20OR%20";
	//Si présence d'espaces dans le nom, tester aussi en les remplaçant par des tirets
	if (strpos($nomautnet, " ") !== false) {
		$atester .= "authFullName_t:\"".$preaut." ".str_replace(" ", "-", $nomautnet)."\"%20OR%20";
		$atester .= "authFullName_t:\"".str_replace(".", "", prenomCompInit($preaut))." ".str_replace(" ", "-", $nomautnet)."\"%20OR%20";
		$atester .= "authFullName_t:\"".prenomCompInit($preaut)." ".str_replace(" ", "-", $nomautnet)."\"%20OR%20";
	}
	//Si présence de tirets dans le nom, tester aussi en les remplaçant par des espaces
	if (strpos($nomautnet, "-") !== false) {
		$atester .= "authFullName_t:\"".$preaut." ".ucwords(str_replace("-", " ", $nomautnet))."\"%20OR%20";
		$atester .= "authFullName_t:\"".str_replace(".", "", prenomCompInit($preaut))." ".ucwords(str_replace("-", " ", $nomautnet))."\"%20OR%20";
		$atester .= "authFullName_t:\"".prenomCompInit($preaut)." ".ucwords(str_replace("-", " ", $nomautnet))."\"%20OR%20";
	}

	//Réitérer les tests avec prénoms et nom 'nettoyés' des caractères accentués
	$preautnet = wd_remove_accents($preaut);
	$nomautnet = wd_remove_accents($nomaut);

	$atester .= "authFullName_t:\"".$preautnet." ".$nomautnet."\"%20OR%20";
	$atester .= "authFullName_t:\"".str_replace(".", "", prenomCompInit($preautnet))." ".$nomautnet."\"%20OR%20";
	$atester .= "authFullName_t:\"".prenomCompInit($preautnet)." ".$nomautnet."\"%20OR%20";
	//Si présence d'espaces dans le nom, tester aussi en les remplaçant par des tirets
	if (strpos($nomautnet, " ") !== false) {
		$atester .= "authFullName_t:\"".$preautnet." ".str_replace(" ", "-", $nomautnet)."\"%20OR%20";
		$atester .= "authFullName_t:\"".str_replace(".", "", prenomCompInit($preautnet))." ".str_replace(" ", "-", $nomautnet)."\"%20OR%20";
		$atester .= "authFullName_t:\"".prenomCompInit($preautnet)." ".str_replace(" ", "-", $nomautnet)."\"%20OR%20";
	}
	//Si présence de tirets dans le nom, tester aussi en les remplaçant par des espaces
	if (strpos($nomautnet, "-") !== false) {
		$atester .= "authFullName_t:\"".$preautnet." ".ucwords(str_replace("-", " ", $nomautnet))."\"%20OR%20";
		$atester .= "authFullName_t:\"".str_replace(".", "", prenomCompInit($preautnet))." ".ucwords(str_replace("-", " ", $nomautnet))."\"%20OR%20";
		$atester .= "authFullName_t:\"".prenomCompInit($preautnet)." ".ucwords(str_replace("-", " ", $nomautnet))."\"%20OR%20";
	}

	//Réitérer si présence d'un nom alternatif
	if (isset($altaut) && $altaut != "") {
		$atester .= "authFullName_t:\"".$preaut." ".$altaut."\"%20OR%20";
		$atester .= "authFullName_t:\"".str_replace(".", "", prenomCompInit($preaut))." ".$altaut."\"%20OR%20";
		$atester .= "authFullName_t:\"".prenomCompInit($preaut)." ".$altaut."\"%20OR%20";
		//Si présence d'espaces dans le nom, tester aussi en les remplaçant par des tirets
		if (strpos($altaut, " ") !== false) {
			$atester .= "authFullName_t:\"".$preaut." ".str_replace(" ", "-", $altaut)."\"%20OR%20";
			$atester .= "authFullName_t:\"".str_replace(".", "", prenomCompInit($preaut))." ".str_replace(" ", "-", $altaut)."\"%20OR%20";
			$atester .= "authFullName_t:\"".prenomCompInit($preaut)." ".str_replace(" ", "-", $altaut)."\"%20OR%20";
		}
		//Si présence de tirets dans le nom, tester aussi en les remplaçant par des espaces
		if (strpos($altaut, "-") !== false) {
			$atester .= "authFullName_t:\"".$preaut." ".ucwords(str_replace("-", " ", $altaut))."\"%20OR%20";
			$atester .= "authFullName_t:\"".str_replace(".", "", prenomCompInit($preaut))." ".ucwords(str_replace("-", " ", $altaut))."\"%20OR%20";
			$atester .= "authFullName_t:\"".prenomCompInit($preaut)." ".ucwords(str_replace("-", " ", $altaut))."\"%20OR%20";
		}
		
		//Réitérer les tests avec prénoms 'nettoyés' des caractères accentués
		$preautnet = wd_remove_accents($preaut);
		
		$atester .= "authFullName_t:\"".$preautnet." ".$altaut."\"%20OR%20";
		$atester .= "authFullName_t:\"".str_replace(".", "", prenomCompInit($preautnet))." ".$altaut."\"%20OR%20";
		$atester .= "authFullName_t:\"".prenomCompInit($preautnet)." ".$altaut."\"%20OR%20";
		//Si présence d'espaces dans le nom, tester aussi en les remplaçant par des tirets
		if (strpos($altaut, " ") !== false) {
			$atester .= "authFullName_t:\"".$preautnet." ".str_replace(" ", "-", $altaut)."\"%20OR%20";
			$atester .= "authFullName_t:\"".str_replace(".", "", prenomCompInit($preautnet))." ".str_replace(" ", "-", $altaut)."\"%20OR%20";
			$atester .= "authFullName_t:\"".prenomCompInit($preautnet)." ".str_replace(" ", "-", $altaut)."\"%20OR%20";
		}
		//Si présence de tirets dans le nom, tester aussi en les remplaçant par des espaces
		if (strpos($altaut, "-") !== false) {
			$atester .= "authFullName_t:\"".$preautnet." ".ucwords(str_replace("-", " ", $altaut))."\"%20OR%20";
			$atester .= "authFullName_t:\"".str_replace(".", "", prenomCompInit($preautnet))." ".ucwords(str_replace("-", " ", $altaut))."\"%20OR%20";
			$atester .= "authFullName_t:\"".prenomCompInit($preautnet)." ".ucwords(str_replace("-", " ", $altaut))."\"%20OR%20";
		}
		
		//Réitérer les tests avec nom 'nettoyé' des caractères accentués
		$altautnet = wd_remove_accents($altaut);
		
		$atester .= "authFullName_t:\"".$preaut." ".$altautnet."\"%20OR%20";
		$atester .= "authFullName_t:\"".str_replace(".", "", prenomCompInit($preaut))." ".$altautnet."\"%20OR%20";
		$atester .= "authFullName_t:\"".prenomCompInit($preaut)." ".$altautnet."\"%20OR%20";
		//Si présence d'espaces dans le nom, tester aussi en les remplaçant par des tirets
		if (strpos($altautnet, " ") !== false) {
			$atester .= "authFullName_t:\"".$preaut." ".str_replace(" ", "-", $altautnet)."\"%20OR%20";
			$atester .= "authFullName_t:\"".str_replace(".", "", prenomCompInit($preaut))." ".str_replace(" ", "-", $altautnet)."\"%20OR%20";
			$atester .= "authFullName_t:\"".prenomCompInit($preaut)." ".str_replace(" ", "-", $altautnet)."\"%20OR%20";
		}
		//Si présence de tirets dans le nom, tester aussi en les remplaçant par des espaces
		if (strpos($altautnet, "-") !== false) {
			$atester .= "authFullName_t:\"".$preaut." ".ucwords(str_replace("-", " ", $altautnet))."\"%20OR%20";
			$atester .= "authFullName_t:\"".str_replace(".", "", prenomCompInit($preaut))." ".ucwords(str_replace("-", " ", $altautnet))."\"%20OR%20";
			$atester .= "authFullName_t:\"".prenomCompInit($preaut)." ".ucwords(str_replace("-", " ", $altautnet))."\"%20OR%20";
		}

		//Réitérer les tests avec prénom et nom 'nettoyés' des caractères accentués
		$preautnet = wd_remove_accents($preaut);
		$altautnet = wd_remove_accents($altaut);
		
		$atester .= "authFullName_t:\"".$preautnet." ".$altautnet."\"%20OR%20";
		$atester .= "authFullName_t:\"".str_replace(".", "", prenomCompInit($preautnet))." ".$altautnet."\"%20OR%20";
		$atester .= "authFullName_t:\"".prenomCompInit($preautnet)." ".$altautnet."\"%20OR%20";
		//Si présence d'espaces dans le nom, tester aussi en les remplaçant par des tirets
		if (strpos($altautnet, " ") !== false) {
			$atester .= "authFullName_t:\"".$preautnet." ".str_replace(" ", "-", $altautnet)."\"%20OR%20";
			$atester .= "authFullName_t:\"".str_replace(".", "", prenomCompInit($preautnet))." ".str_replace(" ", "-", $altautnet)."\"%20OR%20";
			$atester .= "authFullName_t:\"".prenomCompInit($preautnet)." ".str_replace(" ", "-", $altautnet)."\"%20OR%20";
		}
		//Si présence de tirets dans le nom, tester aussi en les remplaçant par des espaces
		if (strpos($altautnet, "-") !== false) {
			$atester .= "authFullName_t:\"".$preautnet." ".ucwords(str_replace("-", " ", $altautnet))."\"%20OR%20";
			$atester .= "authFullName_t:\"".str_replace(".", "", prenomCompInit($preautnet))." ".ucwords(str_replace("-", " ", $altautnet))."\"%20OR%20";
			$atester .= "authFullName_t:\"".prenomCompInit($preautnet)." ".ucwords(str_replace("-", " ", $altautnet))."\"%20OR%20";
		}
	}

	$atester = substr($atester, 0, (strlen($atester) - 8));
	$atester .= ")";
	return $atester;
}

//Suppression des fichiers du dossier HAL créés il y a plus d'une heure
suppression("./HAL", 3600);

//Unicité des fichiers CSV et RTF créés
$unicite = time();

//Constantes générales
if (isset($_GET['lang']) && ($_GET['lang'] != "")) {
  $lang = htmlspecialchars($_GET['lang']);
}else{
  $lang = "fr";
}
if ($lang == "fr") {//français
  $typdocHAL = array("1" => "Articles",
                "2" => "Communications",
                "3" => "Chapitres d'ouvrages scientifiques",
                "4" => "Thèses",
                "5" => "Autres publications",
                "6" => "Autres",
                "7" => "Rapports de recherche",
                "8" => "Images",
                "9" => "Ouvrages scientifiques",
                "10" => "Direction d'ouvrages scientifiques",
                "11" => "Mémoire",
                "12" => "HDR (Habilitation à Diriger des Recherches)",
                "13" => "Brevet",
                "14" => "Poster",
                "15" => "Cours",
                "16" => "Conférences invitées",
                "17" => "Articles avec comité de lecture de revues internationales",
                "18" => "Articles avec comité de lecture de revues nationales",
                "19" => "Articles sans comité de lecture de revues internationales",
                "20" => "Articles sans comité de lecture de revues nationales");
  $form1 = "Tri par année : de ";
  $form2 = " à ";
  $form3 = "Nombre de publications par page : " ;
  $form4c = "Recherche sur un auteur particulier : ";
  $form4s = "Auteur : ";
  $form5c = "Recherche sur un mot du titre : ";
  $form5s = "Mot du titre : ";
  $form6 = "Type de publication (tous par défaut) : ";
  $form7 = "Présentation bibliographique (auteurs.titre.réf biblio) : ";
  $form8 = "Valider";
  $form9c = "Formulaire simple";
  $form9s = "Formulaire complet";
  $form9p = "sfvi";//Sans formulaire et vue intégrale
  $form10 = "BibTex sert à gérer et traiter des bases bibliographiques";
  $form11 = "Fichier PDF";
  $reinit = "Revenir à la liste complète des publications pour l'année en cours";
  $consult1 = "Consultez nos ";
  $consult2 = "publications en libre accès sur HAL";
  $result1 = "De ";
  $result2 = " à ";
  $result3 = "Aucune publication";
  $result3bis = "Affinez vos critères de recherche: plus de 5000 publications constituent l'extraction initiale.";
  $result3_1 = "<u>Remarque :</u> Seuls les ";
  $result3_2 = " premiers auteurs sont affichés.";
  $result4 = "";
  $result5 = " publication(s) :";
  $result6 = "Exporter les données affichées en CSV";
  $result7 = "Exporter les données affichées en RTF";
	$result8 = "Ensemble des références";
	$result9 = "Période";
	$result10 = "";
}else{//anglais
  $typdocHAL = array("1" => "Articles",
                "2" => "Workshop/conference papers",
                "3" => "Book chapters",
                "4" => "Theses",
                "5" => "Other publication",
                "6" => "Other",
                "7" => "Research reports",
                "8" => "Picture",
                "9" => "Books",
                "10" => "Edition of book or proceedings",
                "11" => "Preprints, Working Papers, ...",
                "12" => "Habilitation research",
                "13" => "Patent",
                "14" => "Posters",
                "15" => "Lecture",
                "16" => "Invited conference talk",
                "17" => "International refereed journal articles",
                "18" => "National refereed journal articles",
                "19" => "International non-refereed journal articles",
                "20" => "National non-refereed journal articles");
  $form1 = "Years: from ";
  $form2 = " to ";
  $form3 = "Number of publications per page: ";
  $form4c = "Return articles authored by: ";
  $form4s = "Author: ";
  $form5c = "Search for title words: ";
  $form5s = "Title words: ";
  $form6 = "Publication type (default: all): ";
  $form7 = "Bibliographic display (ie. full bibliographic citation): ";
  $form8 = "Submit";
  $form9c = "Basic form";
  $form9s = "Expanded form";
  $form9p = "sfvi";//No form and full view
  $form10 = "BibTex is used to manage and edit bibliographic databases";
  $form11 = "PDF file";
  $reinit = "Return to the full list of publications for the current year";
  $consult1 = "Check our ";
  $consult2 = "Open Access Repository";
  $result1 = "From ";
  $result2 = " to ";
  $result3 = "No publication";
  $result3bis = "Refine your search criteria: more than 5000 publications constitute the initial extraction.";
  $result3_1 = "<u>Note:</u> when necessary, only the first ";
  $result3_2 = " authors are displayed.";
  $result4 = "";
  $result5 = " publication(s):";
  $result6 = "Export data displayed in CSV";
  $result7 = "Export data displayed in RTF";
	$result8 = "All references";
	$result9 = "Period";
	$result10 = "";
}

$labo = "";
$collection_exp = "";
$priorite = "";
$entite = "";
if (isset($_GET['labo']) && ($_GET['labo'] != "")) {
  $labo = strtoupper(htmlspecialchars($_GET['labo']));
  $priorite = "labo";
  $entite = $labo;
}
if (isset($_GET['collection_exp']) && ($_GET['collection_exp'] != "")) {
  $collection_exp = strtoupper(htmlspecialchars($_GET['collection_exp']));
  $priorite = "collection_exp";
  $entite = $collection_exp;
	
	//Initiation des listes des auteurs appartenant à la collection spécifiée pour la liste
	$listenominit = "~";
  $listenominit2 = "~";
  $listenomcomp1 = "~";
  $listenomcomp2 = "~";
	$arriv = "~";
	$depar = "~";
}

$equipe_recherche_exp = "";
if (isset($_GET['equipe_recherche_exp']) && ($_GET['equipe_recherche_exp'] != "")) {
  $equipe_recherche_exp = htmlspecialchars($_GET['equipe_recherche_exp']);
}
$auteur_exp = "";
if (isset($_GET['auteur_exp']) && ($_GET['auteur_exp'] != "") && strpos($_GET['auteur_exp'], ",") === false) {
  //$auteur_exp = wd_remove_accents(ucwords($_GET['auteur_exp']));
  $auteur_exp = prenomCompEntier(htmlspecialchars($_GET['auteur_exp']));
  //$auteur_exp = str_replace("'", "\'", $auteur_exp);
}else{
  if (isset($_GET['auteur_exp']) && ($_GET['auteur_exp'] != "")) {
    $auteur_exp = htmlspecialchars($_GET['auteur_exp']);
  }
}

$autvar = "";
if (isset($_GET['autvar']) && !empty($_GET['autvar'])) {
	$autvar = wd_remove_accents($_GET['autvar']);
	$tabaut = explode('~', $autvar);
	$preaut = $tabaut[0];
	$nomaut = $tabaut[1];
	//$listenominit = "~".$nomaut." ".$preaut."~".$nomaut." ".substr($preaut, 0, 1).".~".$nomaut." ".substr($preaut, 0, 1)."~";
	//$listenominit2 = "~".$preaut." ".$nomaut."~".substr($preaut, 0, 1).". ".$nomaut."~".substr($preaut, 0, 1)." ".$nomaut."~";
	$listenominit = "~".$nomaut." ".$preaut."~".$nomaut." ".prenomCompInit($preaut)."~".$nomaut." ".substr(prenomCompInit($preaut), 0, -1)."~";
	$listenominit2 = "~".$preaut." ".$nomaut."~".prenomCompInit($preaut)." ".$nomaut."~".substr(prenomCompInit($preaut), 0, -1)." ".$nomaut."~";
	$arriv = "~1900~1900~1900~";
	$moisactuel = date('n', time());
	if ($moisactuel >= 10) {$idepar = date('Y', time())+1;}else{$idepar = date('Y', time());}
	$depar = "~".$idepar."~".$idepar."~".$idepar."~";	
}

//année n ou n+1 ?
$qte = 0;
if (date ('m') == 11 || date ('m') == 12) {
  $anneen = date('Y', time())+1;
  $url = "http://api.archives-ouvertes.fr/search/?wt=xml&q=collCode_s:".$collection_exp."&rows=100000&fq=producedDateY_i:".$anneen;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'SCD (https://halur1.univ-rennes1.fr)');
  curl_setopt($ch, CURLOPT_USERAGENT, 'PROXY (http://siproxy.univ-rennes1.fr)');
  if (isset ($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")	{
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
		curl_setopt($ch, CURLOPT_CAINFO, "cacert.pem");
	}
  $resultat = curl_exec($ch);
  curl_close($ch);
  $dom = new DOMDocument();
  $dom->loadXML($resultat);
  $res0 = $dom->getElementsByTagName('response');
  foreach($res0 as $resgen0) {
    $res1 = $resgen0->getElementsByTagName('result');
    foreach($res1 as $resgen1) {
      if ($resgen1->hasAttribute("numFound")) {
        $qte = $resgen1->getAttribute("numFound");
      }
    }
  }
  if ($qte == 0) {$anneen = date('Y', time());}//Ne pas afficher n+1 s'il n'y a pas de résultat
}else{
  $anneen = date('Y', time());
}
if (isset($_GET['mailto']) && ($_GET['mailto'] != "")) {
  $mailto = htmlspecialchars($_GET['mailto']);
}else{
  $mailto = "toto.titi@univ-rennes1.fr";
}
if (isset($_GET['css']) && ($_GET['css'] != "")) {
  $css = htmlspecialchars($_GET['css']);
}else{
  $css = "https://halur1.univ-rennes1.fr/HAL_SCD.css";
}
if (isset($_GET['bt']) && ($_GET['bt'] != "")) {
  $bt = htmlspecialchars($_GET['bt']);
}else{
  $bt = "oui";
}
$form = "";
if (isset($_GET['form']) && ($_GET['form'] != "")) {
  $form = htmlspecialchars($_GET['form']);
}
if (isset($_GET['authidhal']) && ($_GET['authidhal'] != "")) {
  $form = "aucun";
}
if (isset($_GET['authidhali']) && ($_GET['authidhali'] != "")) {
  $form = "aucun";
}
if (isset($_GET['authid']) && ($_GET['authid'] != "")) {
  $form = "aucun";
}
if (isset($_GET['collection_exp']) && ($_GET['collection_exp'] == "") && isset($_GET['auteur_exp']) && ($_GET['auteur_exp'] != "")) {
  $form = "aucun";
}
//quand les publis ne portent pas forcément l'affiliation à collection_exp > ex: &tous=oui
$tous = "";
if (isset($_GET['tous']) && ($_GET['tous'] != "")) {
  $tous = htmlspecialchars($_GET['tous']);
}

//pour le formulaire simple, année minimale pour l'affichage > ex: &annee_publideb=1998 > on limitera toujours l'affichage à 8 années au maximum
$annee_publideb = "";
if (isset($_GET['annee_publideb']) && ($_GET['annee_publideb'] != "")) {
  $annee_publideb = htmlspecialchars($_GET['annee_publideb']);
}

//précision quant à l'année de publication initiale > ex: &anneedep=2001 > aucune limite dans le nombre d'années à afficher
$anneedep = "";
if (isset($_GET['anneedep']) && ($_GET['anneedep'] != "")) {
  $anneedep = htmlspecialchars($_GET['anneedep']);
}

//pour limiter le nombre d'auteurs à afficher si liste trop longue >  n premiers + et al.
$lim_aut = "";
if (isset($_GET['lim_aut']) && ($_GET['lim_aut'] != "")) {
  $lim_aut = htmlspecialchars($_GET['lim_aut']);
  //On élimine le cas = 0
  if ($lim_aut == 0) {$lim_aut = "";}
}

//Identifiant HAL
$halid = "";
if (isset($_GET['halid']) && ($_GET['halid'] != "")) {
  $halid = htmlspecialchars($_GET['halid']);
}

//Lien Pubmed
$lienpubmed = "";
if (isset($_GET['lienpubmed']) && ($_GET['lienpubmed'] != "")) {
  $lienpubmed = htmlspecialchars($_GET['lienpubmed']);
}

//Identifiant "string" HAL auteur
$authidhal = "";
if (isset($_GET['authidhal']) && ($_GET['authidhal'] != "")) {
  $authidhal = htmlspecialchars($_GET['authidhal']);
}

//Identifiant "num" HAL auteur
$authidhali = "";
if (isset($_GET['authidhali']) && ($_GET['authidhali'] != "")) {
  $authidhali = htmlspecialchars($_GET['authidhali']);
}

//Identifiant "num" HAL auteur
$authid = "";
if (isset($_GET['authid']) && ($_GET['authid'] != "")) {
  $authidhali = htmlspecialchars($_GET['authid']);
}

//Identifiant "num" HAL auteur à exclure
$notauthid = "";
if (isset($_GET['notauthid']) && ($_GET['notauthid'] != "")) {
  $notauthid = htmlspecialchars($_GET['notauthid']);
}

//Identifiant HAL notice à exclure
$nothal = "";
if (isset($_GET['nothal']) && ($_GET['nothal'] != "")) {
  $nothal = htmlspecialchars($_GET['nothal']);
}

//Laisser l’affichage du DOI dans les métadonnées 
$affDoi = "";
if (isset($_GET['affDoi']) && ($_GET['affDoi'] == "oui")) {
  $affDoi = htmlspecialchars($_GET['affDoi']);
}

//Laisser l’affichage de l'IdHal dans les métadonnées 
$affIdh = "";
if (isset($_GET['affIdh']) && ($_GET['affIdh'] == "oui")) {
  $affIdh = htmlspecialchars($_GET['affIdh']);
}

//années à exclure > ex: &annee_excl=(2013,2010)
$annee_excl = "";
if (isset($_GET['annee_excl']) && ($_GET['annee_excl'] != "")) {
  $annee_excl = htmlspecialchars($_GET['annee_excl']);
  //$annee_excl_tab = explode(",",$annee_excl);
}
//type/absence de formulaire
if (isset($_GET['typform']) && ($_GET['typform'] != "")) {$typform = htmlspecialchars($_GET['typform']);}else{$typform = "Formulaire simple";}
if ($annee_publideb != "" || $anneedep != "") {
  if ($annee_publideb != "") {
    $anneedep = $annee_publideb;
		if (is_numeric($anneen) && is_numeric($annee_publideb)) {$nbanneesfs = $anneen - $annee_publideb;}else{$nbanneesfs = 8;}
    if ($nbanneesfs >= 8) {$nbanneesfs = 8;}
  }else{
		if (is_numeric($anneen) && is_numeric($anneedep)) {$nbanneesfs = $anneen - $anneedep;}else{$nbanneesfs = 8;}
  }
}else{
  if ($typform == $form9s || $typform == $form9p) {//formulaire complet ou vue intégrale sans formulaire
    $anneedep = 1970;//année jusqu'où remonter dans le formulaire complet
  }else{
    $nbanneesfs = 8;//nombre d'années à afficher dans le formulaire simplifié
  }
}

//affichage des détails de type de documents (ACLI, ACLN, ...)
$detail = "";
if (isset($_GET['detail']) && ($_GET['detail'] != "")) {
  $detail = htmlspecialchars($_GET['detail']);
}

//mise en forme spéciale (ISCR par exemple)
$mef = "";
if (isset($_GET['mef']) && ($_GET['mef'] != "")) {
  $mef = htmlspecialchars($_GET['mef']);
}

//ID structure pour mise en valeur des auteurs
$ids = "";
if (isset($_GET['ids']) && ($_GET['ids'] != "")) {
  $ids = htmlspecialchars($_GET['ids']);
}

//Couleurs primaire et secondaire
$primary = "";
$secondary = "";
if (isset($_GET['primary']) && ($_GET['primary'] != "")) {
  $primary = htmlspecialchars($_GET['primary']);
}
if (isset($_GET['secondary']) && ($_GET['secondary'] != "")) {
  $secondary = htmlspecialchars($_GET['secondary']);
}

$premautab = array();
$auteurs = array();
$auteursinit = array();
$typdoctab = array();
$titrehref = array();
$rvnp = array();
$doi = array();
$idhal = array();
$bibtex = array();
$pdf1 = array();
$pdf2 = array();
$pdf3 = array();
$pdf4 = array();
$pdf5 = array();
$reprint = array();
$indtab = array();

//recherche jusqu'à quelle année il y a des publications > routine trop longue !!! > d'où $nbanneesfc ...
$a = $anneen;
//$plong = 9;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Les publications HAL <?php echo($entite);?></title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
	<script src="./Publis-HAL-SCD.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.1/MathJax.js?config=TeX-AMS-MML_HTMLorMML"></script>
  <script type='text/x-mathjax-config'>
    MathJax.Hub.Config({tex2jax: {inlineMath: [['$','$'], ['$$','$$']]}});
  </script>

  <link href="bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo($css);?>" type="text/css">
	<?php
	if (isset($_GET['frame']) && ($_GET['frame'] == "oui")) {
	?>
	<script>
			function resizeIframe() {

					var docHeight;
					if (typeof document.height != 'undefined') {
							docHeight = document.height;
					}
					else if (document.compatMode && document.compatMode != 'BackCompat') {
							docHeight = document.documentElement.scrollHeight;
					}
					else if (document.body
							&& typeof document.body.scrollHeight != 'undefined') {
							docHeight = document.body.scrollHeight;
					}

					// magic number: suppress generation of scrollbars...
					docHeight += 70;

					parent.document.getElementById('the_iframe').style.height = docHeight + "px";
			}
			parent.document.getElementById('the_iframe').onload = resizeIframe;
			parent.window.onresize = resizeIframe;
	</script>
	<?php } ?>
</head>
<body>
<?php
$mailtocrit = "";
if ((isset($_GET['ipas']))  && ($typform == $form9s || $typform == $form9p)) {$ipas = htmlspecialchars($_GET['ipas']);}else{$ipas = 10;}
if (isset($_GET['ideb'])) {$ideb = htmlspecialchars($_GET['ideb']);}else{$ideb = 1;}
if (!is_numeric($ipas) || !is_numeric($ideb)) {die();}
if (isset($_GET['ifin'])) {$ifin = htmlspecialchars($_GET['ifin']);}else{$ifin = $ideb + $ipas - 1;}
if (!is_numeric($ifin)) {die();}
if (isset($_GET['typord'])) {$typord = htmlspecialchars($_GET['typord']);}else{$typord = "desc";}
if (isset($_GET['presbib']) && ($_GET['presbib'] != "br")) {$presbibtxt = " checked";$presbib = "&nbsp;-&nbsp;";}else{$presbibtxt = "";$presbib = "<br>";}
if (isset($_GET['labocrit'])) {
  $labosur = explode(";", $labo);
  $mailtosur = explode(";", $mailto);
  $labocrit = htmlspecialchars($_GET['labocrit']);
  $ii = 0;
  //while ($labosur[$ii] != "") {
  while (isset($labosur[$ii])) {
    if ($labocrit == $labosur[$ii]) {$mailtocrit = $mailtosur[$ii];}
    $ii++;
  }
}else{
  if ($priorite == "collection_exp") {
    $labocrit = $collection_exp;
  }else{
    $labocrit = $labo;
  }
}
unset($labosur, $mailtosur);

if (isset($_GET['aut'])) {$aut = mb_convert_case(htmlspecialchars($_GET['aut']),MB_CASE_LOWER,"UTF-8");}else{$aut = "";}
if (isset($_GET['titre'])) {$titre = mb_convert_case(htmlspecialchars($_GET['titre']),MB_CASE_LOWER,"UTF-8");}else{$titre = "";}
//if (isset($_GET['typdoc']) && ($_GET['typdoc'] != "") && ($typform == $form9s)) {$typdocinit = htmlspecialchars($_GET['typdoc']); $typdoc = "('".$_GET['typdoc']."')";}else{$typdocinit = ""; $typdoc = "";}
if (isset($_GET['typdoc']) && ($_GET['typdoc'] != "")) {$typdocinit = htmlspecialchars($_GET['typdoc']); $typdoc = htmlspecialchars($_GET['typdoc']);}else{$typdocinit = ""; $typdoc = "";}
if (isset($_GET['anneedeb'])) {$anneedeb = htmlspecialchars($_GET['anneedeb']);}else{$anneedeb = $anneen;$anneefin = $anneen;}
if (isset($_GET['anneefin'])) {$anneefin = htmlspecialchars($_GET['anneefin']);}else{if (isset($_GET['anneedeb'])) {$anneefin = htmlspecialchars($_GET['anneedeb']);}else{$anneefin = $anneedeb;}}
// vérification sur ordre des années si différentes
if ($anneefin < $anneedeb) {$anneetemp = $anneedeb; $anneedeb = $anneefin; $anneefin = $anneetemp;}
//$text = "<div id='res_script'><div style='text-align: center;'><h2><b>".$labo." - Publications</b></h2></div><br>\r\n";
$text = "<br>";
if ($typform == $form9p) {//sans formulaire et vue intégrale
  if (isset($_GET['anneedeb'])) {$anneedeb = htmlspecialchars($_GET['anneedeb']);}else{$anneedeb = "1970";}
  if (isset($_GET['anneefin'])) {$anneefin = htmlspecialchars($_GET['anneefin']);}else{$anneefin = $anneefin = $anneen;}
	if (isset($_GET['mef']) && $_GET['mef'] != 1) {
    if (isset($_GET['ensref']) && $_GET['ensref'] == "oui") {
      echo ("<p class='etendueAnnees'><h3>".$result8."</h3></p>");
    }else{
      echo ("<p class='etendueAnnees'><h3>".$result9." ".$anneedeb." - ".$anneefin."</h3></p>");
    }
  }
}
if ($typform == $form9s) {//formulaire de recherche complet
  $text .= "<div><form method='GET' accept-charset='utf-8' action='".$_SERVER['REQUEST_URI']."' style='margin:0px; width:500px'>\r\n";
  //année de publication
  $text .= "<div class='form-inline'>";
  $text .= "<label for='anneedeb'>".$form1."</label>&nbsp;";
  $text .= "<select size='1' id='anneedeb' name='anneedeb' class='form-control' style='margin:0px; width:80px'>\r\n";
  $annee = $anneen;
  while($annee >= $anneedep) {
    if ($anneedeb == $annee) {$txt = " selected";}else{$txt = "";}
    if (strpos($annee_excl, strval($annee)) === false) {
      $text .= "<option value='".$annee."'".$txt.">".$annee."</option>\r\n";
    }
    $annee--;
  }
  $text .= "</select>\r\n";
  $text .= "&nbsp;<label for='anneefin'>".$form2."</label>&nbsp;";
  $text .= "<select size='1' id='anneefin' name='anneefin' class='form-control' style='margin:0px; width:80px'>\r\n";
  $annee = $anneen;
  while($annee >= $anneedep) {
    if ($anneefin == $annee) {$txt = " selected";}else{$txt = "";}
    if (strpos($annee_excl, strval($annee)) === false) {
      $text .= "<option value='".$annee."'".$txt.">".$annee."</option>\r\n";
    }
    $annee--;
  }
  $text .= "</select><br>\r\n";
  $text .= "</div>";
  //intervalle d'affichage des résultats
  //$text .= $form3."\r\n";
  //$text .= "<input type='text' name='ipas' value='".$ipas."' size='1'><br>\r\n";
  //recherche sur un auteur
  $text .= "<div class='form-inline'>";
  $text .= "<label for='aut'>".$form4c."</label>\r\n";
  $text .= "<input type='text' id='aut' name='aut' value='".$aut."' class='form-control' style='margin:0px; width:200px'><br>\r\n";
  $text .= "</div>";
  //recherche sur un mot du titre
  $text .= "<div class='form-inline'>";
  $text .= "<label for='titre'>".$form5c."</label>\r\n";
  $text .= "<input type='text' id='titre' name='titre' value='".$titre."' class='form-control' style='margin:0px; width:200px'><br>\r\n";
  $text .= "</div>";
  //recherche par type de support
  $text .= "<div class='form-inline'>";
  $text .= "<label for='typdoc'>".$form6."</label><br>\r\n";
  $text .= "<select size='3' id='typdoc' name='typdoc' class='form-control' style='margin:0px; width:300px'>";
  if($typdoc == "ART") {$txt = " selected";}else{$txt = "";}
  $text .= "<option value='ART'".$txt.">".$typdocHAL['1']."</option>\r\n";
  if($typdoc == "COMM") {$txt = " selected";}else{$txt = "";}
  $text .= "<option value='COMM'".$txt.">".$typdocHAL['2']."</option>\r\n";
  if($typdoc == "COUV") {$txt = " selected";}else{$txt = "";}
  $text .= "<option value='COUV'".$txt.">".$typdocHAL['3']."</option>\r\n";
  if($typdoc == "THESE") {$txt = " selected";}else{$txt = "";}
  $text .= "<option value='THESE'".$txt.">".$typdocHAL['4']."</option>\r\n";
  if($typdoc == "UNDEFINED") {$txt = " selected";}else{$txt = "";}
  $text .= "<option value='UNDEFINED'".$txt.">".$typdocHAL['5']."</option>\r\n";
  if($typdoc == "OTHER") {$txt = " selected";}else{$txt = "";}
  $text .= "<option value='OTHER'".$txt.">".$typdocHAL['6']."</option>\r\n";
  if($typdoc == "REPORT") {$txt = " selected";}else{$txt = "";}
  $text .= "<option value='REPORT'".$txt.">".$typdocHAL['7']."</option>\r\n";
  if($typdoc == "IMG") {$txt = " selected";}else{$txt = "";}
  $text .= "<option value='IMG'".$txt.">".$typdocHAL['8']."</option>\r\n";
  if($typdoc == "OUV") {$txt = " selected";}else{$txt = "";}
  $text .= "<option value='OUV'".$txt.">".$typdocHAL['9']."</option>\r\n";
  if($typdoc == "DOUV") {$txt = " selected";}else{$txt = "";}
  $text .= "<option value='DOUV'".$txt.">".$typdocHAL['10']."</option>\r\n";
  if($typdoc == "MEM") {$txt = " selected";}else{$txt = "";}
  $text .= "<option value='MEM'".$txt.">".$typdocHAL['11']."</option>\r\n";
  if($typdoc == "HDR") {$txt = " selected";}else{$txt = "";}
  $text .= "<option value='HDR'".$txt.">".$typdocHAL['12']."</option>\r\n";
  if($typdoc == "PATENT") {$txt = " selected";}else{$txt = "";}
  $text .= "<option value='PATENT'".$txt.">".$typdocHAL['13']."</option>\r\n";
  if($typdoc == "POSTER") {$txt = " selected";}else{$txt = "";}
  $text .= "<option value='POSTER'".$txt.">".$typdocHAL['14']."</option>\r\n";
  if($typdoc == "LECTURE") {$txt = " selected";}else{$txt = "";}
  $text .= "<option value='LECTURE'".$txt.">".$typdocHAL['15']."</option>\r\n";
  if($typdoc == "PRESCONF") {$txt = " selected";}else{$txt = "";}
  $text .= "<option value='PRESCONF'".$txt.">".$typdocHAL['16']."</option>\r\n";
  $text .= "</select><br>\r\n";
  $text .= "</div>";
  //présentation bibliographique
  $text .= "<div class='form-inline'>";
  $text .= "<label for='presbib'>".$form7."</label>\r\n";
  $text .= "<input type='checkbox' id='presbib' name='presbib' value='ok' ".$presbibtxt." class='form-control' style='margin: 0px; height: 15px;'><br>\r\n";
  $text .= "</div>";
  $text .= "<input type='hidden' name='labo' value='".$labo."'>\r\n";
  $text .= "<input type='hidden' name='collection_exp' value='".$collection_exp."'>\r\n";
  $text .= "<input type='hidden' name='equipe_recherche_exp' value='".$equipe_recherche_exp."'>\r\n";
  $text .= "<input type='hidden' name='auteur_exp' value='".$auteur_exp."'>\r\n";
  $text .= "<input type='hidden' name='mailto' value='".$mailto."'>\r\n";
  $text .= "<input type='hidden' name='lang' value='".$lang."'>\r\n";
  $text .= "<input type='hidden' name='css' value='".$css."'>\r\n";
  $text .= "<input type='hidden' name='bt' value='".$bt."'>\r\n";
  $text .= "<input type='hidden' name='form' value='".$form."'>\r\n";
  $text .= "<input type='hidden' name='tous' value='".$tous."'>\r\n";
  $text .= "<input type='hidden' name='annee_publideb' value='".$annee_publideb."'>\r\n";
  if ($typform != $form9s) {//formulaire simple
    $text .= "<input type='hidden' name='anneedep' value='".$anneedep."'>\r\n";
  }
  $text .= "<input type='hidden' name='lim_aut' value='".$lim_aut."'>\r\n";
  $text .= "<input type='hidden' name='annee_excl' value='".$annee_excl."'>\r\n";
  $text .= "<input type='hidden' name='ideb' value='1'>\r\n";
  $text .= "<input type='hidden' name='typform' value='".$form9s."'>\r\n";
  $text .= "<br><input type='submit' class='btn btn-md btn-primary' value='".$form8."'>&nbsp;&nbsp;&nbsp;";
  $text .= "<input type='submit' class='btn btn-md btn-primary' name='typform' value='".$form9c."'>";
  $text .= "</form><br>";
}else{//formulaire de recherche simplifié ou sans formulaire
  //années
  if ($halid == "" && $typform != $form9p) {
    if ($anneedeb != $anneefin) {$anneedeb = $anneen; $anneefin = $anneen;}
    $text .= "<div style='text-align: center;'><h3>\r\n";
    $i = $anneen;
    while ($i >= $anneen - $nbanneesfs) {
      //on vérifie si ce n'est pas une année à exclure
      if (strpos($annee_excl, strval($i)) === false) {
				$presbibUrl = "";
				if ($presbib =="<br>") {$presbibUrl = "br";}
        $text .= "<a href=\"?autvar=".$autvar."&labo=".$labo."&collection_exp=".$collection_exp."&equipe_recherche_exp=".$equipe_recherche_exp."&auteur_exp=".$auteur_exp."&mailto=".$mailto."&lang=".$lang."&css=".$css."&form=".$form."&tous=".$tous."&annee_publideb=".$annee_publideb."&anneedep=".$anneedep."&lim_aut=".$lim_aut."&annee_excl=".$annee_excl."&bt=".$bt."&presbib=".$presbibUrl."&labocrit=".$labocrit."&typdoc=".$typdoc."&typform=".str_replace(' ', '%20', $typform)."&anneedeb=".$i."&anneefin=".$i."&titre=".$titre."&aut=".$aut."&authidhal=".$authidhal."&authidhali=".$authidhali."&authid=".$authid."&notauthid=".$notauthid."&nothal=".$nothal."&lienpubmed=".$lienpubmed."&mef=".$mef."&ids=".$ids."&primary=".$primary."&secondary=".$secondary."&detail=".$detail."&affDoi=".$affDoi."&affIdh=".$affIdh."&ipas=".$ipas."&typord=".$typord."&acc=noninit\">".$i."</a>&nbsp;&nbsp;&nbsp;\r\n";
      }
      $i--;
    }
    $text .= "\r\n</h3></div>\r\n";
  }
  if ($form != "aucun" && $typform != $form9p) {
    $text .= "<br><div><form method='GET' accept-charset='utf-8' action='".$_SERVER['REQUEST_URI']."' style='margin:0px; width:500px'>\r\n";
    //recherche sur un auteur
    $text .= "<div class='form-inline'>";
    $text .= "<label for='aut'>".$form4s."</label>\r\n";
    $text .= "<input type='text' id='aut' name='aut' class='form-control' value='".$aut."' style='width:200px'>&nbsp;&nbsp;&nbsp;\r\n";
    //recherche sur un mot du titre
    $text .= "</div>";
    $text .= "<div class='form-inline'>";   
    $text .= "<label for='titre'>".$form5s."</label>\r\n";
    $text .= "<input type='text' id='titre' name='titre' class='form-control' value='".$titre."' style='width:200px'>&nbsp;&nbsp;&nbsp;\r\n<br>";
    $text .= "</div>";
    $text .= "<div class='form-inline'>"; 
    $text .= "<label for='presbib'>".$form7."</label>\r\n";
    $text .= "<input type='checkbox' id ='presbib' name='presbib' class='form-control' value='ok' style='height: 15px;' ".$presbibtxt."><br>\r\n";
    $text .= "</div>";
    $text .= "<input type='hidden' name='labo' value='".$labo."'>\r\n";
    $text .= "<input type='hidden' name='collection_exp' value='".$collection_exp."'>\r\n";
    $text .= "<input type='hidden' name='equipe_recherche_exp' value='".$equipe_recherche_exp."'>\r\n";
    $text .= "<input type='hidden' name='auteur_exp' value='".$auteur_exp."'>\r\n";
    $typdoc2 = str_replace(array("(","'",")"),"",$typdoc);
    $text .= "<input type='hidden' name='typdoc' value='".$typdoc2."'>\r\n";
    $text .= "<input type='hidden' name='mailto' value='".$mailto."'>\r\n";
    $text .= "<input type='hidden' name='lang' value='".$lang."'>\r\n";
    $text .= "<input type='hidden' name='css' value='".$css."'>\r\n";
    $text .= "<input type='hidden' name='bt' value='".$bt."'>\r\n";
    $text .= "<input type='hidden' name='form' value='".$form."'>\r\n";
    $text .= "<input type='hidden' name='tous' value='".$tous."'>\r\n";
    $text .= "<input type='hidden' name='annee_publideb' value='".$annee_publideb."'>\r\n";
    $text .= "<input type='hidden' name='anneedep' value='".$anneedep."'>\r\n";
    $text .= "<input type='hidden' name='lim_aut' value='".$lim_aut."'>\r\n";
    $text .= "<input type='hidden' name='annee_excl' value='".$annee_excl."'>\r\n";
    $text .= "<input type='hidden' name='ipas' value='".$ipas."'>\r\n";
		$text .= "<input type='hidden' name='typord' value='".$typord."'>\r\n";
    $text .= "<input type='hidden' name='ideb' value='1'>\r\n";
    $text .= "<input type='hidden' name='typform' value='".$form9c."'>\r\n";
    $text .= "<br><input class='btn btn-md btn-primary' type='submit' value='".$form8."'>&nbsp;&nbsp;&nbsp;";
    $text .= "<input type='submit' class='btn btn-md btn-primary' name='typform' value='".$form9s."'>";
    $text .= "</form><br>";
  }
}

if (((isset($_GET['autvar']) && $_GET['autvar'] != "") || (isset($_GET['aut']) && $_GET['aut'] != "") || (isset($_GET['titre']) && $_GET['titre'] != "") || (isset($_GET['typdoc']) && $_GET['typdoc'] != "(\'ART\',\'COMM\')")) && (($typform != $form9p) && (isset($_GET['form']) && $_GET['form'] != "aucun") && (isset($_GET['acc']) && $_GET['acc'] != "init"))) {
	$text .= "<div class='retour'><a href='".$_SERVER['PHP_SELF']."?autvar=".$autvar."&labo=".$labo."&collection_exp=".$collection_exp."&equipe_recherche_exp=".$equipe_recherche_exp."&auteur_exp=".$auteur_exp."&mailto=".$mailto."&lang=".$lang."&css=".$css."&form=".$form."&tous=".$tous."&annee_publideb=".$annee_publideb."&anneedep=".$anneedep."&lim_aut=".$lim_aut."&annee_excl=".$annee_excl."&bt=".$bt."&typform=".str_replace(' ', '%20', $typform)."&typdoc=".$typdoc."&detail=".$detail."&ids=".$ids."&primary=".$primary."&secondary=".$secondary."&acc=init'>".$reinit."</a></div><br><br>\r\n";
}

$labo2 = $labocrit;

if ($form != "aucun" && $typform != $form9p) {
  $text .= $consult1."<a target='_blank' href='http://hal-univ-rennes1.archives-ouvertes.fr/".$labo2."/' class='noicon'>".$consult2."</a>.<br>\r\n";
}

if ($halid == "" && $typform != $form9p) {
  if ($anneedeb == $anneefin) {
    $text .= "<br><p class='Rubrique'>".$anneedeb."</p><br>\r\n";
  }else{
    $text .= "<br><p class='Rubrique'>".$result1.$anneedeb.$result2.$anneefin."</p><br>\r\n";
  }
}

if ($labocrit != "" && $labocrit != $labo) {
  $labosur[0] = $labocrit;
  $mailtosur[0] = $mailtocrit;
}else{
  $labosur = explode(";", $labo);
  $mailtosur = explode(";", $mailto);
}

if (strpos($mailto, ",") !== false) {
  $mailtosur = explode(";", $mailto);
}

//var_dump($labosur);
$ii = 0;
$i = 1;
$labocrit2 = "";

//while ($labosur[$ii] != "") {
while (isset($labosur[$ii])) {
  $labocrit = $labosur[$ii];
  if (strpos($mailto, ",") !== false) {
    $mailto = $mailtosur[$ii];
  }
  //$mailto = $mailtosur[$ii];

  //if ($lang == "fr") {
    //if ($tous == "oui") {
      //$HAL_URL = "http://hal.archives-ouvertes.fr/Public/afficheRequetePubli.php?typdoc=".$typdoc."&annee_publideb=".$anneedeb."&annee_publifin=".$anneefin."&auteur_exp=".$auteur_exp."&CB_typdoc=oui&CB_auteur=oui&CB_titre=oui&CB_article=oui&CB_DOI=oui&langue=Francais&tri_exp=annee_publi&tri_exp2=typdoc&tri_exp3=date_publi&ordre_aff=TA&Fen=Aff";
    //}else{
      //$HAL_URL = "http://hal.archives-ouvertes.fr/Public/afficheRequetePubli.php?typdoc=".$typdoc."&annee_publideb=".$anneedeb."&annee_publifin=".$anneefin."&collection_exp=".$labocrit."&equipe_recherche_exp=".$equipe_recherche_exp."&auteur_exp=".$auteur_exp."&CB_typdoc=oui&CB_auteur=oui&CB_titre=oui&CB_article=oui&CB_DOI=oui&langue=Francais&tri_exp=annee_publi&tri_exp2=typdoc&tri_exp3=date_publi&ordre_aff=TA&Fen=Aff";
    //}
  //}else{
    //if ($tous == "oui") {
      //$HAL_URL = "http://hal.archives-ouvertes.fr/Public/afficheRequetePubli.php?typdoc=".$typdoc."&annee_publideb=".$anneedeb."&annee_publifin=".$anneefin."&auteur_exp=".$auteur_exp."&CB_typdoc=oui&CB_auteur=oui&CB_titre=oui&CB_article=oui&CB_DOI=oui&langue=Anglais&tri_exp=annee_publi&tri_exp2=typdoc&tri_exp3=date_publi&ordre_aff=TA&Fen=Aff";
    //}else{
      //$HAL_URL = "http://hal.archives-ouvertes.fr/Public/afficheRequetePubli.php?typdoc=".$typdoc."&annee_publideb=".$anneedeb."&annee_publifin=".$anneefin."&collection_exp=".$labocrit."&equipe_recherche_exp=".$equipe_recherche_exp."&auteur_exp=".$auteur_exp."&CB_typdoc=oui&CB_auteur=oui&CB_titre=oui&CB_article=oui&CB_DOI=oui&langue=Anglais&tri_exp=annee_publi&tri_exp2=typdoc&tri_exp3=date_publi&ordre_aff=TA&Fen=Aff";
    //}
  //}

  //Extraction des résultats
  $dom = new DomDocument;
  //$URL = 'http://api-preprod.archives-ouvertes.fr/search/?wt=xml&q=labStructCode_s:"UMR6553"&fq=producedDateY_i:"2014"&fl=title_s,label_s,uri_s,abstract_s,docType_s,doiId_s,label_bibtex,keyword_s,authFullName_s&sort=auth_sort asc';
  //$URL = 'http://api-preprod.archives-ouvertes.fr/search/?wt=xml&q=labStructAcronym_s:"GR"&rows=100000&fq=producedDateY_i:"2014" AND producedDateY_i:"2013"&fl=title_s,label_s,producedDateY_i,uri_s,journalTitle_s,abstract_s,docType_s,doiId_s,keyword_s,authFullName_s,bookTitle_s,conferenceTitle_s,&sort=auth_sort asc';
  $root = 'http';
	if ( isset ($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")	{
    $root.= "s";
	}
  $URL = $root.'://api.archives-ouvertes.fr/search/?wt=xml';
  if ($tous == "oui") {
    $URL .= '&rows=100000&fq=';
  }else{
    //$URL .= '&q=labStructAcronym_s:"'.$labocrit.'"';
    $URL .= '&q=collCode_s:"'.$labocrit.'"';
  }
  if (isset($equipe_recherche_exp)) {
    $URL .= '&rteamStructName_s:"'.$equipe_recherche_exp.'"';
  }
  $URL .= '&rows=100000&fq=';
  if ($anneedeb != $anneefin) {
    $iann = $anneedeb;
    while ($iann <= $anneefin) {
      if ($iann == $anneedeb) {$URL .= " (";}else{$URL .= " OR";}
      $URL .= ' producedDateY_i:"'.$iann.'"';
      $iann++;
    }
    $URL .= ')';
  }else{
    $URL .= 'producedDateY_i:"'.$anneedeb.'"';
    //if ($anneefin != "") {$URL .= ' OR producedDateY_i:"'.$anneefin.'"';}
  }

  if ($auteur_exp != "") {
    if (strpos($auteur_exp, ",") === false) {
      $URL .= ' AND authFullName_t:"'.$auteur_exp.'"';
    }else{
      $diffaut = explode(",", $auteur_exp);
      $iaut = 0;
      while (isset($diffaut[$iaut])) {
        if ($iaut == 0) {$URL .= " AND (";}else{$URL .= " OR";}
        $auteur_exp2 = $diffaut[$iaut];
        $URL .= ' authFullName_t:"'.$auteur_exp2.'"';
				$URL .= ' OR authFullName_t:"'.str_replace(".", "", $auteur_exp2).'"';
        $iaut++;
      }
      $URL .= ')';
    }
  }
	if (!empty($autvar)) {
		$anneedeb = 1970;
		$anneefin = date('Y', time());
		$collection_exp = "";
		if (!empty($_GET["collection_exp"])) {
			$collection_exp = $_GET["collection_exp"];
			$URL = $root.'://api.archives-ouvertes.fr/search/?wt=xml&rows=100000&fq=producedDateY_i:["'.$anneedeb.'" TO "'.$anneefin.'"] AND collCode_s:'.$collection_exp.' AND '.varAut($autvar);
		}else{
			$URL = $root.'://api.archives-ouvertes.fr/search/?wt=xml&rows=100000&fq=producedDateY_i:["'.$anneedeb.'" TO "'.$anneefin.'"] AND '.varAut($autvar);
		}
		//echo $URL;
	}
	
  if ($auteur_exp != "" && $collection_exp == "") {
    //On limite l'URL à juste une recherche sur auteur_exp toutes collections confondues, mais en ajoutant après le type de documents recherché
    $URL = $root.'://api.archives-ouvertes.fr/search/?wt=xml&rows=100000&fq=';
    if (strpos($auteur_exp, ",") === false) {
      $URL .= 'authFullName_t:"'.$auteur_exp.'"';
    }else{
      $diffaut = explode(",", $auteur_exp);
      $iaut = 0;
      while (isset($diffaut[$iaut])) {
        if ($iaut == 0) {$URL .= "(";}else{$URL .= " OR";}
        $auteur_exp2 = $diffaut[$iaut];
        $URL .= ' authFullName_t:"'.$auteur_exp2.'"';
				$URL .= ' OR authFullName_t:"'.str_replace(".", "", $auteur_exp2).'"';
        $iaut++;
      }
      $URL .= ')';
    }
    if ($anneedeb != $anneefin) {
      $iann = $anneedeb;
      while ($iann <= $anneefin) {
        if ($iann == $anneedeb) {$URL .= " AND (";}else{$URL .= " OR";}
        $URL .= ' producedDateY_i:"'.$iann.'"';
        $iann++;
      }
      $URL .= ')';
    }else{
      $URL .= ' AND producedDateY_i:"'.$anneedeb.'"';
      //if ($anneefin != "") {$URL .= ' OR producedDateY_i:"'.$anneefin.'"';}
    }
  }

  if ($typdocinit != "") {
    if (strpos($typdocinit, ",") === false) {
      $URL .= ' AND docType_s:"'.$typdocinit.'"';
    }else{
      $diffdoc = explode(",", $typdocinit);
      $idoc = 0;
      while (isset($diffdoc[$idoc])) {
        if ($idoc == 0) {$URL .= " AND (";}else{$URL .= " OR";}
        $typdoc = $diffdoc[$idoc];
        $URL .= ' docType_s:"'.$typdoc.'"';
        $idoc++;
      }
      $URL .= ')';
    }
  }

  if ($halid != "") {
    //On limite l'URL à juste une recherche sur halId_s, mais en ajoutant après le type de documents recherché
    $URL = $root.'://api.archives-ouvertes.fr/search/?wt=xml&rows=100000&fq=';
    if (strpos($halid, ",") === false) {
      $URL .= 'halId_s:"'.$halid.'"';
    }else{
      $diffhalid = explode(",", $halid);
      $ihal = 0;
      while (isset($diffhalid[$ihal])) {
        if ($ihal == 0) {$URL .= " (";}else{$URL .= " OR";}
        $halid = $diffhalid[$ihal];
        $URL .= ' halId_s:"'.$halid.'"';
        $ihal++;
      }
      $URL .= ')';
    }

    if ($typdocinit != "") {
      if (strpos($typdocinit, ",") === false) {
        $URL .= ' AND docType_s:"'.$typdocinit.'"';
      }else{
        $diffdoc = explode(",", $typdocinit);
        $idoc = 0;
        while (isset($diffdoc[$idoc])) {
          if ($idoc == 0) {$URL .= " AND (";}else{$URL .= " OR";}
          $typdoc = $diffdoc[$idoc];
          $URL .= ' docType_s:"'.$typdoc.'"';
          $idoc++;
        }
        $URL .= ')';
      }
    }
  }

  if ($authidhal != "") {
    //On limite l'URL à juste une recherche sur authIdHal_s, mais en ajoutant après le type de documents recherché
    $URL = $root.'://api.archives-ouvertes.fr/search/?wt=xml&rows=100000&fq=';
    if (strpos($authidhal, ",") === false) {
      $URL .= 'authIdHal_s:"'.$authidhal.'"';
    }else{
      $diffauthidhal = explode(",", $authidhal);
      $ihal = 0;
      while (isset($diffauthidhal[$ihal])) {
        if ($ihal == 0) {$URL .= " (";}else{$URL .= " OR";}
        $authidhal = $diffauthidhal[$ihal];
        $URL .= ' authIdHal_s:"'.$authidhal.'"';
        $ihal++;
      }
      $URL .= ')';
    }
    if ($anneedeb != $anneefin) {
      $iann = $anneedeb;
      while ($iann <= $anneefin) {
        if ($iann == $anneedeb) {$URL .= " AND (";}else{$URL .= " OR";}
        $URL .= ' producedDateY_i:"'.$iann.'"';
        $iann++;
      }
      $URL .= ')';
    }else{
      $URL .= ' AND producedDateY_i:"'.$anneedeb.'"';
      //if ($anneefin != "") {$URL .= ' OR producedDateY_i:"'.$anneefin.'"';}
    }

    if ($typdocinit != "") {
      if (strpos($typdocinit, ",") === false) {
        $URL .= ' AND docType_s:"'.$typdocinit.'"';
      }else{
        $diffdoc = explode(",", $typdocinit);
        $idoc = 0;
        while (isset($diffdoc[$idoc])) {
          if ($idoc == 0) {$URL .= " AND (";}else{$URL .= " OR";}
          $typdocinit = $diffdoc[$idoc];
          $URL .= ' docType_s:"'.$typdocinit.'"';
          $idoc++;
        }
        $URL .= ')';
      }
    }
  }

  if ($authidhali != "") {
    //On limite l'URL à juste une recherche sur authIdHal_i, mais en ajoutant après le type de documents recherché
    $URL = $root.'://api.archives-ouvertes.fr/search/?wt=xml&rows=100000&fq=';
    if (strpos($authidhali, ",") === false) {
      $URL .= 'authIdHal_i:"'.$authidhali.'"';
    }else{
      $diffauthidhali = explode(",", $authidhali);
      $ihal = 0;
      while (isset($diffauthidhali[$ihal])) {
        if ($ihal == 0) {$URL .= " (";}else{$URL .= " OR";}
        $authidhali = $diffauthidhali[$ihal];
        $URL .= ' authIdHal_i:"'.$authidhali.'"';
        $ihal++;
      }
      $URL .= ')';
    }
    if ($anneedeb != $anneefin) {
      $iann = $anneedeb;
      while ($iann <= $anneefin) {
        if ($iann == $anneedeb) {$URL .= " AND (";}else{$URL .= " OR";}
        $URL .= ' producedDateY_i:"'.$iann.'"';
        $iann++;
      }
      $URL .= ')';
    }else{
      $URL .= ' AND producedDateY_i:"'.$anneedeb.'"';
      //if ($anneefin != "") {$URL .= ' OR producedDateY_i:"'.$anneefin.'"';}
    }

    if ($typdocinit != "") {
      if (strpos($typdocinit, ",") === false) {
        $URL .= ' AND docType_s:"'.$typdocinit.'"';
      }else{
        $diffdoc = explode(",", $typdocinit);
        $idoc = 0;
        while (isset($diffdoc[$idoc])) {
          if ($idoc == 0) {$URL .= " AND (";}else{$URL .= " OR";}
          $typdocinit = $diffdoc[$idoc];
          $URL .= ' docType_s:"'.$typdocinit.'"';
          $idoc++;
        }
        $URL .= ')';
      }
    }
  }

  if ($authid != "") {
    //On limite l'URL à juste une recherche sur authId_i, mais en ajoutant après le type de documents recherché
    $URL = $root.'://api.archives-ouvertes.fr/search/?wt=xml&rows=100000&fq=';
    if (strpos($authid, ",") === false) {
      $URL .= 'authId_i:"'.$authid.'"';
    }else{
      $diffauthid = explode(",", $authid);
      $ihal = 0;
      while (isset($diffauthid[$ihal])) {
        if ($ihal == 0) {$URL .= " (";}else{$URL .= " OR";}
        $authid = $diffauthid[$ihal];
        $URL .= ' authId_i:"'.$authid.'"';
        $ihal++;
      }
      $URL .= ')';
    }
    if ($anneedeb != $anneefin) {
      $iann = $anneedeb;
      while ($iann <= $anneefin) {
        if ($iann == $anneedeb) {$URL .= " AND (";}else{$URL .= " OR";}
        $URL .= ' producedDateY_i:"'.$iann.'"';
        $iann++;
      }
      $URL .= ')';
    }else{
      $URL .= ' AND producedDateY_i:"'.$anneedeb.'"';
      //if ($anneefin != "") {$URL .= ' OR producedDateY_i:"'.$anneefin.'"';}
    }

    if ($typdocinit != "") {
      if (strpos($typdocinit, ",") === false) {
        $URL .= ' AND docType_s:"'.$typdocinit.'"';
      }else{
        $diffdoc = explode(",", $typdocinit);
        $idoc = 0;
        while (isset($diffdoc[$idoc])) {
          if ($idoc == 0) {$URL .= " AND (";}else{$URL .= " OR";}
          $typdocinit = $diffdoc[$idoc];
          $URL .= ' docType_s:"'.$typdocinit.'"';
          $idoc++;
        }
        $URL .= ')';
      }
    }
  }

  if ($notauthid != "") {//auteur à exclure
    if (strpos($notauthid, ",") === false) {
      $URL .= ' NOT (authId_i:"'.$notauthid.'")';
    }else{
      $diffnotauthid = explode(",", $notauthid);
      $ihal = 0;
      while (isset($diffnotauthid[$ihal])) {
        if ($ihal == 0) {$URL .= " NOT (";}else{$URL .= " OR";}
        $notauthid = $diffnotauthid[$ihal];
        $URL .= ' authId_i:"'.$notauthid.'"';
        $ihal++;
      }
      $URL .= ')';
    }
  }

  if ($nothal != "") {//notice à exclure
    if (strpos($nothal, ",") === false) {
      $URL .= ' NOT (halId_s:"'.$nothal.'")';
    }else{
      $diffnothal = explode(",", $nothal);
      $ihal = 0;
      while (isset($diffnothal[$ihal])) {
        if ($ihal == 0) {$URL .= " NOT (";}else{$URL .= " OR";}
        $nothalID = $diffnothal[$ihal];
        $URL .= ' halId_s:"'.$nothalID.'"';
        $ihal++;
      }
      $URL .= ')';
    }
  }

	$URL .= '&fl=title_s,subTitle_s,label_s,producedDateY_i,uri_s,journalTitle_s,abstract_s,docType_s,doiId_s,keyword_s,authFullName_s,authFullName_t,bookTitle_s,conferenceTitle_s,fileMain_s,files_s,halId_s,label_bibtex,volume_s,issue_s,page_s,journalPublisher_s,scientificEditor_s,pubmedId_s,audience_s,peerReviewing_s,authIdHalFullName_fs,authFirstName_s,language_s,authLastName_s,authIdHasPrimaryStructure_fs&sort=auth_sort asc';
  $URL = str_replace(" ", "%20", $URL);
  //echo ("toto : ".$URL);

  //$dom->load($URL);

  $removeBom = function($var) { return preg_replace('/\\0/', "", $var); };
	$ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $removeBom($URL));
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'SCD (https://halur1.univ-rennes1.fr)');
  curl_setopt($ch, CURLOPT_USERAGENT, 'PROXY (http://siproxy.univ-rennes1.fr)');
	if (isset ($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")	{
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
		curl_setopt($ch, CURLOPT_CAINFO, "cacert.pem");
	}
  $resultat = curl_exec($ch);

  if(!isset($resultat) || $resultat == "") {
		echo ('<br><br><b><div style=\'text-align: center;\'><big>Contenu temporairement indisponible. Merci de votre patience / Content temporarily unavailable. We apologize for the inconvenience</big></div></b>');
		die;
	}
	
	//$resultat = str_replace("&","and",$resultat);
  $resultat = str_replace(" < "," &#60; ",$resultat);
  //$resultat = str_replace(">", ">\r\n", $resultat);
  //echo "Début<br>".$resultat."Fin<br>";
  
  curl_close($ch);
  $dom = new DOMDocument();
  $dom->loadXML($resultat);

  //if ($dom->getElementsByTagName('result') && $dom->getElementsByTagName('result')->item(0)->hasAttribute('numFound')) {
	if ($dom->getElementsByTagName('result')->item(0)) {
		$resinit = $dom->getElementsByTagName('result')->item(0)->getAttribute('numFound');
	}else{
		die("Requête erronée");
	}
	
	//Recherche des auteurs de la collection grâce aux affiliations
	if ($ids != "~" && empty($autvar)) {
		$contents = file_get_contents(str_replace("wt=xml&", "", $URL));
		//$contents = utf8_encode($contents);
		$results = json_decode($contents);
		$numFound = 0;
		if (isset($results->response->numFound)) {$numFound=$results->response->numFound;}
		if ($numFound != 0) {
			$tabId = explode(",", $ids);
			foreach($tabId as $Id) {
			 if ($Id != "") {
				 foreach($results->response->docs as $entry){
					 foreach($entry->authIdHasPrimaryStructure_fs as $auth){
						 $tabAuth = explode("_FacetSep_", $auth);
						 if (strpos($tabAuth[1], $Id) !== false) {//Auteur de la collection
							 $tabQ = explode("_JoinSep_", $tabAuth[1]);
							 $indQ = 0;
							 foreach($entry->authFullName_s as $funa){
								 //if ($funa == $tabQ[0] && strpos($listenominit, $entry->authFirstName_s[$indQ]) === false) {
								 if ($funa == $tabQ[0] && strpos($listenominit, nomCompEntier($entry->authLastName_s[$indQ])." ".prenomCompInit($entry->authFirstName_s[$indQ])) === false) {
									 $prenom = prenomCompInit($entry->authFirstName_s[$indQ]);
									 $listenominit .= nomCompEntier($entry->authLastName_s[$indQ])." ".$prenom.".~";
									 $listenominit2 .= $prenom." ".nomCompEntier($entry->authLastName_s[$indQ])."~";
									 $arriv .= "1900~";
									 $moisactuel = date('n', time());
									 if ($moisactuel >= 10) {$idepar = date('Y', time())+1;}else{$idepar = date('Y', time());}
									 $depar .= $idepar."~";
									 break;
								 }
								 $indQ++;
							 }
						 }
					 }
				 }
			 }
			}
		}
	}
	
  $anneepre = $anneedeb - 1;
  if ($resinit == 0 && $anneepre == date('Y', time())) {//Si, en fin d'année n, il n'y a pas de résultat, on recherche sur l'année n-1
    $URL = str_replace($anneedeb, $anneepre, $URL);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $URL);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'SCD (https://halur1.univ-rennes1.fr)');
    curl_setopt($ch, CURLOPT_USERAGENT, 'PROXY (http://siproxy.univ-rennes1.fr)');
    if (isset ($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")	{
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
		curl_setopt($ch, CURLOPT_CAINFO, "cacert.pem");
	}
    $resultat = curl_exec($ch);
    $resultat = str_replace(" < "," &#60; ",$resultat);
    curl_close($ch);
    $dom = new DOMDocument();
    $dom->loadXML($resultat);
    if($lang == "fr") {
      $result10 = "<b><font color='red'>Il n'y a pas encore de résultat pour ".$anneedeb.". Voici ceux de ".$anneepre." :</font></b><br>";
    }else{
      $result10 = "<b><font color='red'>There are no results for ".$anneedeb." yet. Here are the ones from ".$anneepre.":</font></b><br>";
    }
  }

  $i = 1;
  $res0 = $dom->getElementsByTagName('doc');
  foreach($res0 as $resgen0) {
    $res1 = $resgen0->getElementsByTagName('str');
    foreach($res1 as $resgen1) {
      if ($resgen1->hasAttribute("name")) {
        $quoi = $resgen1->getAttribute("name");
        //if (strpos("label_bibtex",$quoi) !== false) {$label_bibtex[$i] = $resgen1->nodeValue;}
        if (strpos("uri_s",$quoi) !== false) {$uri[$i] = $resgen1->nodeValue;}
        if (strpos("fileMain_s",$quoi) !== false) {$pdf1[$i] = $resgen1->nodeValue;}
        if (strpos("docType_s",$quoi) !== false) {$typdocxml[$i] = $resgen1->nodeValue;}
        if (strpos("label_s",$quoi) !== false) {$label[$i] = $resgen1->nodeValue;}
        if (strpos("doiId_s",$quoi) !== false) {$doi[$i] = $resgen1->nodeValue;}
				if (strpos("halId_s",$quoi) !== false) {$idhal[$i] = $resgen1->nodeValue;}
        if ($lienpubmed != "non") {
          if (strpos("pubmedId_s",$quoi) !== false) {$pubmed[$i] = $resgen1->nodeValue;}
        }
        if (strpos("journalTitle_s",$quoi) !== false) {$journal[$i] = $resgen1->nodeValue;}
        if (strpos("bookTitle_s",$quoi) !== false) {$livre[$i] = $resgen1->nodeValue;}
        if (strpos("conferenceTitle_s",$quoi) !== false) {$colloque[$i] = $resgen1->nodeValue;}
        if (strpos("volume_s",$quoi) !== false) {$volume[$i] = $resgen1->nodeValue;}
        if (strpos("page_s",$quoi) !== false) {$page[$i] = $resgen1->nodeValue;}
        if ($mef == 1 && isset($page[$i]) && $page[$i] != "") {$label[$i] = str_replace('pp.'.$page[$i], $page[$i], $label[$i]);}//Mise en forme spéciale > suppression 'pp.' devant les pages
        if (strpos("journalPublisher_s",$quoi) !== false) {$journalPublisher[$i] = $resgen1->nodeValue;}
        if ($mef == 1 && isset($journalPublisher[$i]) && $journalPublisher[$i] != "") {$label[$i] = str_replace(', '.$journalPublisher[$i], '', $label[$i]);}//Mise en forme spéciale > suppression journalPublisher
        if (strpos("scientificEditor_s",$quoi) !== false) {$scientificEditor[$i] = $resgen1->nodeValue;}
        if (strpos("audience_s",$quoi) !== false) {$audience[$i] = $resgen1->nodeValue;}
        if (strpos("peerReviewing_s",$quoi) !== false) {$peerrev[$i] = $resgen1->nodeValue;}
      }
    }
    $res2 = $resgen0->getElementsByTagName('arr');
		
    foreach($res2 as $resgen2) {
      if ($resgen2->hasAttribute("name")) {
        $quoi = $resgen2->getAttribute("name");
        if (strpos("abstract_s",$quoi) !== false) {$abstract[$i] = $resgen2->nodeValue;}
        if (strpos("language_s",$quoi) !== false) {$language[$i] = $resgen2->nodeValue;}
        //if (strpos("publisher_s",$quoi) !== false) {$publication[$i] = $resgen2->nodeValue;}
        //if (strpos("scientificEditor_s",$quoi) !== false) {$editeur[$i] = $resgen2->nodeValue;}
        //if (strpos("issue_s",$quoi) !== false) {$issue[$i] = $resgen2->nodeValue;}
        if (strpos("title_s",$quoi) !== false) {
          $titleliste = "";
          $enfants = $resgen2->childNodes;
          foreach($enfants as $enfant) {
            $titleliste .= $enfant->nodeValue;
          }
          //$titreseul[$i] = $titleliste;
					$titreseul[$i] = $resgen2->nodeValue;
        }
        if (strpos("subTitle_s",$quoi) !== false) {
          $subtitleliste = "";
          $enfants = $resgen2->childNodes;
          foreach($enfants as $enfant) {
            $subtitleliste .= $enfant->nodeValue;
          }
          $subtitle[$i] = $subtitleliste;
        }
        if (strpos("issue_s",$quoi) !== false) {
          $issueliste = "";
          $enfants = $resgen2->childNodes;
          foreach($enfants as $enfant) {
            $issueliste .= $enfant->nodeValue . ", ";
          }
          $issueliste = substr($issueliste, 0, (strlen($issueliste)-2));
          $issue[$i] = $issueliste;
        }
        if ($mef == 1 && isset($issue[$i]) && $issue[$i] != "") {$label[$i] = str_replace(' ('.$issue[$i].')', '', $label[$i]);}//Mise en forme spéciale > suppression issue
        if (strpos("keyword_s",$quoi) !== false) {
          $keywliste = "";
          $enfants = $resgen2->childNodes;
          foreach($enfants as $enfant) {
            $keywliste .= $enfant->nodeValue . ", ";
          }
          $keywliste = substr($keywliste, 0, (strlen($keywliste)-2));
          $keyword[$i] = $keywliste;
        }
        if (strpos("files_s",$quoi) !== false) {
          $filesliste = "";
          $enfants = $resgen2->childNodes;
          foreach($enfants as $enfant) {
            $filesliste .= $enfant->nodeValue;
          }
          $pdf1[$i] = $filesliste;
        }
        $authidhal_mev = "";
        if ($authidhal != "" && strpos("authIdHalFullName_fs",$quoi) !== false) {
          $enfants = $resgen2->childNodes;
          foreach($enfants as $enfant) {
            if (strpos($enfant->nodeValue,$authidhal) !== false) {
              $authidhal_mev .= str_replace($authidhal."_FacetSep_", "", $enfant->nodeValue);
              //echo $authidhal_mev;
            }
          }
        }
        if (strpos("authFirstName_s",$quoi) !== false) {
          $prenoms = $resgen2->childNodes;
        }
        if (strpos("authFullName_s",$quoi) !== false) {
          $cpt = 1;
          $autliste = "";
          $autetal = "";
          $enfants = $resgen2->childNodes;
          foreach($enfants as $enfant) {
            //echo 'toto : '.$prenoms->item($cpt-1)->textContent.' - '.$enfant->nodeValue.'<br>';
						if (isset($prenoms->item($cpt-1)->textContent)) {$prenomauteur = $prenoms->item($cpt-1)->textContent;}else{$prenomauteur = "";}
            $nomabrege = trim($enfant->nodeValue);
						if (!empty($nomabrege)) {
							$nomabrege = str_replace($prenomauteur, prenomCompInit($prenomauteur), $nomabrege);
							$autliste .= $nomabrege . ", ";
							if($cpt <= 5) {$autetal .= $enfant->nodeValue . ", ";}
							$cpt++;
						}
          }
          $cpt--;
          $testetal = substr($autetal, (strlen($autetal)-8), 6);
          if($testetal != "Et Al.") {
            if($cpt > 5) {
              $autetal .= "et al.";
            }else{
              $autetal = substr($autetal, 0, (strlen($autetal)-2));
            }
          }else{
            $autetal = substr($autetal, 0, (strlen($autetal)-2));
          }
          $auteursetal[$i] = $autetal;
          //$autliste = substr($autliste, 0, (strlen($autliste)-2));
          //$autliste = wd_remove_accents($autliste);
					
          if ($lim_aut != "") {
            $cpt = 1;
            $pospv = 0;
            $lim_aut_ok = 1;
            while ($cpt <= $lim_aut) {
              if (strpos($autliste, ",", $pospv+1) !== false) {
                $pospv = strpos($autliste, ",", $pospv+1);
                $cpt ++;
              }else{
                $lim_aut_ok = 0;
                break;
              }
            }
            $auteursinit[$i] = $autliste;
            if ($lim_aut_ok != 0) {
              $auteurs[$i] = substr($autliste, 0, $pospv);
              $auteurs[$i] .= " <i> et al.</i>";
            }else{
              if ($presbib == "<br>") {
                $auteurs[$i] = substr($autliste, 0, (strlen($autliste)-2));
              }else{
                $auteurs[$i] = $autliste;
              }
            }
            //echo $lim_aut_ok."<br>";
            //echo $auteur_temp."<br>";
          }else{
            if ($presbib == "<br>") {
              $auteurs[$i] = substr($autliste, 0, (strlen($autliste)-2));
              $auteursinit[$i] = $autliste;
            }else{
              $auteurs[$i] = $autliste;
              $auteursinit[$i] = $autliste;
            }
          }
          //echo "toto :".substr($autliste, (strlen($autliste)-2), 1)."<br>";
          //$auteurs[$i] = str_replace(",  ", " ", $auteurs[$i]);
          $auteurs[$i] .= $presbib;
        }
      }
    }

    $res3 = $resgen0->getElementsByTagName('int');
    foreach($res3 as $resgen3) {
      if ($resgen3->hasAttribute("name")) {
        $quoi = $resgen3->getAttribute("name");
        if (strpos("conferenceStartDateM_i",$quoi) !== false) {$collmois[$i] = $resgen3->nodeValue;}
        if (strpos("producedDateY_i",$quoi) !== false) {$prodate[$i] = $resgen3->nodeValue;}
      }
    }
    $i++;
  }

  $i = 1;
  while (isset($label[$i])) {
    if(isset($uri[$i]) && isset($titreseul[$i])) {
      if(strpos($uri[$i], "http") !== false) {
        $uri[$i] = str_replace(array("http://","https://","http//","https//"), "", $uri[$i]);
      }
      if (strpos($uri[$i], "in2p3") === false && strpos($uri[$i], "inserm") === false) {
        $titrehref[$i] = "<a target='_blank' href='https://".$uri[$i]."'>".$titreseul[$i]."</a>".$presbib;
      }else{
        $titrehref[$i] = "<a target='_blank' href='http://".$uri[$i]."'>".$titreseul[$i]."</a>".$presbib;
      }
    }
    if(isset($doi[$i])) {$doinit[$i] = $doi[$i]; $doi[$i] = "DOI : <a target='_blank' href='https://doi.org/".$doi[$i]."'>https://doi.org/".$doi[$i]."</a>".$presbib;}
    if(isset($pubmed[$i])) {$pubmedinit[$i] = $pubmed[$i]; $pubmed[$i] = "Pubmed : <a target='_blank' href='http://www.ncbi.nlm.nih.gov/pubmed/".$pubmed[$i]."'>".$pubmed[$i]."</a>".$presbib;}
    if(isset($pdf1[$i])) {
      if(strpos($pdf1[$i], "http") !== false) {
          $pdf1[$i] = str_replace(array("http://","https://","http//","https//"), "", $pdf1[$i]);
      }
      if (strpos($pdf1[$i], "inserm") === false) {
        $pdf1[$i] = "<dd class='ValeurRes PDF' style='display: inline; margin-left: 0%;'><a target='_blank' href='https://".$pdf1[$i]."'><img alt='".$form11."' src='https://halur1.univ-rennes1.fr/PDF_icon.gif' style='height: 13px; border:0;' title='PDF' /></a></dd>";
      }else{
        $pdf1[$i] = "<dd class='ValeurRes PDF' style='display: inline; margin-left: 0%;'><a target='_blank' href='http://".$pdf1[$i]."'><img alt='".$form11."' src='https://halur1.univ-rennes1.fr/PDF_icon.gif' style='height: 13px; border:0;' title='PDF' /></a></dd>";
      }
    }
    if (isset($typdocxml[$i])) {
      if ($typdocxml[$i] == "ART") {
        if ($detail == "oui") {
          if (isset($audience[$i]) && isset($peerrev[$i])) {
            if ($audience[$i] == "2" && $peerrev[$i] == "1") {$typdoctab[$i] = $typdocHAL[17];}
            if ($audience[$i] != "2" && $peerrev[$i] == "1") {$typdoctab[$i] = $typdocHAL[18];}
            if ($audience[$i] == "2" && $peerrev[$i] == "0") {$typdoctab[$i] = $typdocHAL[19];}
            if ($audience[$i] != "2" && $peerrev[$i] == "0") {$typdoctab[$i] = $typdocHAL[20];}
          }else{
            if ($language[$i] == "fr") {
              $typdoctab[$i] = $typdocHAL[18];
            }else{
              $typdoctab[$i] = $typdocHAL[17];
            }
          }
        }else{
          $typdoctab[$i] = $typdocHAL[1];
        }
        
      }
      if ($typdocxml[$i] == "COMM") {$typdoctab[$i] = $typdocHAL[2];}
      if ($typdocxml[$i] == "COUV") {$typdoctab[$i] = $typdocHAL[3];}
      if ($typdocxml[$i] == "THESE") {$typdoctab[$i] = $typdocHAL[4];}
      if ($typdocxml[$i] == "UNDEFINED") {$typdoctab[$i] = $typdocHAL[5];}
      if ($typdocxml[$i] == "OTHER") {$typdoctab[$i] = $typdocHAL[6];}
      if ($typdocxml[$i] == "REPORT") {$typdoctab[$i] = $typdocHAL[7];}
      if ($typdocxml[$i] == "IMG") {$typdoctab[$i] = $typdocHAL[8];}
      if ($typdocxml[$i] == "OUV") {$typdoctab[$i] = $typdocHAL[9];}
      if ($typdocxml[$i] == "DOUV") {$typdoctab[$i] = $typdocHAL[10];}
      if ($typdocxml[$i] == "MEM") {$typdoctab[$i] = $typdocHAL[11];}
      if ($typdocxml[$i] == "HDR") {$typdoctab[$i] = $typdocHAL[12];}
      if ($typdocxml[$i] == "PATENT") {$typdoctab[$i] = $typdocHAL[13];}
      if ($typdocxml[$i] == "POSTER") {$typdoctab[$i] = $typdocHAL[14];}
      if ($typdocxml[$i] == "LECTURE") {$typdoctab[$i] = $typdocHAL[15];}
      if ($typdocxml[$i] == "PRESCONF") {$typdoctab[$i] = $typdocHAL[16];}
    }
    $test = $label[$i];
    $test = str_replace("..", ".", $test);
    $test = str_replace($auteursetal[$i].". ", "", $test);
    $test = str_replace($auteursetal[$i], "", $test);
    $test = str_replace($titreseul[$i].". ", "", $test);
    if (isset($doinit[$i])) {$test = str_replace("&lt;".$doinit[$i]."&gt;", "", $test);}
    if (isset($pubmedinit[$i])) {$test = str_replace("&lt;".$pubmedinit[$i]."&gt;", "", $test);}
		$url = "";
    if (isset($uri[$i])) {
      $url = str_replace(array("http://", "https://"), "",$uri[$i]);
      $pos = strpos($url, "/")+1;
      $url = substr($url, $pos, (strlen($url)-$pos));
      $bibtex[$i] = "<a target='_blank' href='https://halur1.univ-rennes1.fr/Publis-HAL-SCD-bibtex.php?id=".$url."'><img alt='".$form10."' src='https://halur1.univ-rennes1.fr/BIB_icon.gif' style='height: 13px; border:0;' title='BibTex' /></a> ";
      $test = str_replace("&lt;".$url."&gt;", "", $test);
      $test = str_replace(", et al. ", "", $test);
      $test = str_replace(". .", ".", $test);
      $test = str_replace(", .", ".", $test);
    }
    if(isset($journal[$i])) {$test = str_replace($journal[$i], "<i>".$journal[$i]."</i>", $test);}
    if(isset($livre[$i])) {$test = str_replace($livre[$i], "<i>".$livre[$i]."</i>", $test);}
    if(isset($colloque[$i])) {$test = str_replace($colloque[$i], "<i>".$colloque[$i]."</i>", $test);}
    //echo $i.' => '.$label[$i].'<br>';
    //echo $i.'bis => '.$test.'<br>';
    $rvnp[$i] = $test.$presbib;
		$rvnp[$i] = str_replace(trim($titreseul[$i]).". ", "", $rvnp[$i]);
		if (isset($doinit[$i]) && $affDoi != "oui") {$rvnp[$i] = str_replace("&#x27E8;".$doinit[$i]."&#x27E9;. ", "", $rvnp[$i]);}
		if (isset($idhal[$i]) && $affIdh != "oui") {$rvnp[$i] = str_replace("&#x27E8;".$idhal[$i]."&#x27E9;", "", $rvnp[$i]);}
    $rvnp[$i] = str_replace("  ", "", $rvnp[$i]);
    //Demande reprint par mail
    if ($mailto != "aucun") {
      $repr = "&nbsp;<a href='mailto:".$mailto."?subject=Reprint%20request&amp;body=Would%20you%20please%20send%20me%20a%20copy%20of%20the%20following%20article:%20";
      $repr .= str_replace(array("'", " ", "[", "]"), array("’", "%20", "%5B", "%5D"), strip_tags($auteurs[$i]));
      $repr .= "%20-%20";
      $repr .= str_replace(array("'", " ", "[", "]"), array("’", "%20", "%5B", "%5D"), strip_tags($titreseul[$i]));
      $repr .= "%20-%20";
      $repr .= str_replace(array("'", " ", "[", "]"), array("’", "%20", "%5B", "%5D"), strip_tags($rvnp[$i]));
      $repr .= "%20Many%20thanks%20for%20considering%20my%20request.";
      $repr .= "'><img style='border:0;' src='https://halur1.univ-rennes1.fr/ReprintRequest.jpg' alt='Reprint request: Subject to availability' title='Reprint request: Subject to availability'></a>";
      $repr .= "<br>";
    }else{
      $repr = "&nbsp;";
    }
    $reprint[$i] = $repr;

    $i++;
  }


  $affin = "0";
  //if (strpos($resultat, "Affinez vos critères") !== false) {
    //$affin = "1";
  //}

  if ($labocrit2 == "") {$labocrit2 = $labocrit;}else{$labocrit2 .= ";".$labocrit;}
  $ii++;
}

$imax = $i-1;
$irec = $imax;

//Création d'un tableau avec juste le nom du premier auteur
for ($i = 1; $i <= $imax; $i++) {
  $totaut = $auteurs[$i];
  $pos1 = strpos($totaut, " ");
  $pos2 = strpos($totaut, ",");
  if ($pos2 !== false) {
    $premaut = substr($totaut, $pos1, ($pos2 - $pos1));
  }else{
    $premaut = substr($totaut, $pos1, (strlen($totaut) - $pos1));
  }
  $premautab[$i] = $premaut;
}
//Remplissage des valeurs vides par '-' pour pouvoir ordonnancer les tableaux
for ($i = 1; $i <= $imax; $i++) {
  if (!isset($premautab[$i])) {$premautab[$i] = "-";}
  if (!isset($auteurs[$i])) {$auteurs[$i] = "-";}
  if (!isset($auteursinit[$i])) {$auteursinit[$i] = "-";}
  if (!isset($typdoctab[$i])) {$typdoctab[$i] = "-";}
  if (!isset($titrehref[$i])) {$titrehref[$i] = "-";}
  if (!isset($subtitle[$i])) {$subtitle[$i] = "-";}
  if (!isset($rvnp[$i])) {$rvnp[$i] = "-";}
  if (!isset($prodate[$i])) {$prodate[$i] = "-";}
  if (!isset($journal[$i])) {$journal[$i] = "-";}
  if (!isset($volume[$i])) {$volume[$i] = "-";}
  if (!isset($issue[$i])) {$issue[$i] = "-";}
  if (!isset($page[$i])) {$page[$i] = "-";}
  if (!isset($journalPublisher[$i])) {$journalPublisher[$i] = "-";}
  if (!isset($scientificEditor[$i])) {$scientificEditor[$i] = "-";}
  if (!isset($doi[$i])) {$doi[$i] = "-";}
  if (!isset($pubmed[$i])) {$pubmed[$i] = "-";}
  if (!isset($bibtex[$i])) {$bibtex[$i] = "-";}
  if (!isset($pdf[$i])) {$pdf[$i] = "-";}
  for($j = 1; $j <= 5; $j++)  {
    //if (${"pdf".$j}[$i] == "") {${"pdf".$j}[$i] = "-";}
    if (!isset(${"pdf".$j}[$i])) {${"pdf".$j}[$i] = "-";}
  }
  if ($reprint[$i] == "") {$reprint[$i] = "-";}
}
//var_dump($auteurs);
//tri des tableaux selon leurs clés => réindexation ordonnée
if (!empty($subtitle)) {
  ksort($premautab);
  ksort($auteurs);
  ksort($auteursinit);
  ksort($typdoctab);
  ksort($titrehref);
  ksort($subtitle);
  ksort($rvnp);
  ksort($prodate);
  ksort($journal);
  ksort($volume);
  ksort($issue);
  ksort($page);
  ksort($journalPublisher);
  ksort($scientificEditor);
  ksort($doi);
  ksort($pubmed);
  ksort($bibtex);
  ksort($pdf1);
  ksort($pdf2);
  ksort($pdf3);
  ksort($pdf4);
  ksort($pdf5);
  ksort($reprint);
  //ksort($indtab);

  //array_multisort($typdoctab, $premautab, $auteurs, $titrehref, $rvnp, $doi, $bibtex, $pdf1, $pdf2, $pdf3, $pdf4, $pdf5, $reprint, $indtab);
  //if (strpos($css, "ipr") !== false) {
  if ($typform == "sfvi") {
		if (isset($_GET['typord']) && ($_GET['typord'] == "asc")) {
			array_multisort($typdoctab, $prodate, SORT_ASC, $premautab, $auteurs, $auteursinit, $titrehref, $subtitle, $rvnp, $journal, $volume, $issue, $page, $journalPublisher, $scientificEditor, $doi, $pubmed, $bibtex, $pdf1, $pdf2, $pdf3, $pdf4, $pdf5, $reprint);
		}else{
			array_multisort($typdoctab, $prodate, SORT_DESC, $premautab, $auteurs, $auteursinit, $titrehref, $subtitle, $rvnp, $journal, $volume, $issue, $page, $journalPublisher, $scientificEditor, $doi, $pubmed, $bibtex, $pdf1, $pdf2, $pdf3, $pdf4, $pdf5, $reprint);
		}
  }else{
		if ($anneedeb != $anneefin) {//Si recherche sur différentes années, classer par années décroissantes, puis par auteurs
			array_multisort($typdoctab, $prodate, SORT_DESC, $premautab, $auteurs, $auteursinit, $titrehref, $subtitle, $rvnp, $journal, $volume, $issue, $page, $journalPublisher, $scientificEditor, $doi, $pubmed, $bibtex, $pdf1, $pdf2, $pdf3, $pdf4, $pdf5, $reprint);
		}else{
			array_multisort($typdoctab, $premautab, $auteurs, $auteursinit, $titrehref, $subtitle, $rvnp, $prodate, $journal, $volume, $issue, $page, $journalPublisher, $scientificEditor, $doi, $pubmed, $bibtex, $pdf1, $pdf2, $pdf3, $pdf4, $pdf5, $reprint);
		}
  }
}

//pour la correspondance entre index
for ($i = 1; $i <= $imax; $i++) {
  $indtab[$i] = $i-1;
}
//var_dump($indtab);

if (($titre != "") && ($aut != "")) {//si recherche sur un mot du titre et un auteur
  $irec = 0;
  for ($i = 0; $i < $imax; $i++) {
    if ((stripos($titrehref[$i], $titre) !== false) && (stripos($auteursinit[$i], $aut) !== false)) {$irec++;$indtab[$irec]=$i;}
  }
}
if (($titre != "") && ($aut == "")) {//si recherche juste sur le titre
  $irec = 0;
  for ($i = 0; $i < $imax; $i++) {
    if (stripos($titrehref[$i], $titre) !== false) {$irec++;$indtab[$irec]=$i;}
  }
}
if (($titre == "") && ($aut != "")) {//si recherche juste sur l'auteur
  $irec = 0;
  for ($i = 0; $i < $imax; $i++) {
    if (stripos($auteursinit[$i], $aut) !== false) {$irec++;$indtab[$irec]=$i;}
  }
}

if ($irec == 0) {
  if ($affin == "1") {
    $text .= "<b>".$result3bis."</b><br><br>";
  }else{
    $text .= "<b>".$result3."</b><br><br>";
  }
}else{
  if ($halid == "") {
    $text .= $result10."<b>".$result4.$irec.$result5."</b><br><br>";
  }
}

if ($lim_aut != "") {
  $text .= "<i>".$result3_1.$lim_aut.$result3_2."</i><br><br>";
}

//export en CSV
$Fnm1 = "./HAL/publisHAL_".$unicite.".csv";
$inF = fopen($Fnm1,"w");

fseek($inF, 0);
$chaine = "\xEF\xBB\xBF";
if (isset($_GET['presbib']) && ($_GET['presbib'] != "br")) {
  if ($bt == "oui") {
    if ($mef != 1) {
      $chaine .= "Auteurs;Titre;Sous-titre;Revue;Editeur;Année;Volume;Numéro;Pages;DOI;PMID;bibtex;pdf1;pdf2;pdf3;pdf4;pdf5;reprint";
    }else{
      $chaine .= "Auteurs;Titre;Sous-titre;Revue;Année;Volume;Pages;DOI;PMID;bibtex;pdf1;pdf2;pdf3;pdf4;pdf5;reprint";
    }
  }else{
    if ($mef != 1) {
      $chaine .= "Auteurs;Titre;Sous-titre;Revue;Editeur;Année;Volume;Numéro;Pages;DOI;PMID;pdf1;pdf2;pdf3;pdf4;pdf5;reprint";
    }else{
      $chaine .= "Auteurs;Titre;Sous-titre;Revue;Année;Volume;Pages;DOI;PMID;pdf1;pdf2;pdf3;pdf4;pdf5;reprint";
    }
  }
}else{
  if ($bt == "oui") {
    if ($mef != 1) {
      $chaine .= "Titre;Auteurs;Sous-titre;Revue;Editeur;Année;Volume;Numéro;Pages;DOI;PMID;bibtex;pdf1;pdf2;pdf3;pdf4;pdf5;reprint";
    }else{
      $chaine .= "Titre;Auteurs;Sous-titre;Revue;Année;Volume;Pages;DOI;PMID;bibtex;pdf1;pdf2;pdf3;pdf4;pdf5;reprint";
    }
  }else{
    if ($mef != 1) {
      $chaine .= "Titre;Auteurs;Sous-titre;Revue;Editeur;Année;Volume;Numéro;Pages;DOI;PMID;pdf1;pdf2;pdf3;pdf4;pdf5;reprint";
    }else{
      $chaine .= "Titre;Auteurs;Sous-titre;Revue;Année;Volume;Pages;DOI;PMID;pdf1;pdf2;pdf3;pdf4;pdf5;reprint";
    }
  }
}
fwrite($inF,$chaine.chr(13).chr(10));
$chaine = "";

//export en RTF
$Fnm2 = "./HAL/publisHAL_".$unicite.".rtf";
require_once ("./lib/phprtflite-1.2.0/lib/PHPRtfLite.php");

PHPRtfLite::registerAutoloader();
$rtf = new PHPRtfLite();
$sect = $rtf->addSection();
$font = new PHPRtfLite_Font(10, 'Arial', '#000000', '#FFFFFF');
$fontlien1 = new PHPRtfLite_Font(10, 'Arial', '#A71817', '#FFFFFF');
$fontlien1->setUnderline();
$fontlien2 = new PHPRtfLite_Font(10, 'Arial', '#0071bb', '#FFFFFF');
$fontlien2->setUnderline();
$parFormat = new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_JUSTIFY);

$rubr = "";

if (isset($_GET['typord']) && ($_GET['typord'] == "asc" || $_GET['typord'] == "descInv")) {
	if ($ifin == $irec) {
		$cpt = $ifin - $ideb + 1;
	}else{
		$cpt = $irec - $ifin + $ipas;
	}
}else{
	if ($ifin > $irec) {$ifin = $irec;}
	$cpt = $ideb;
}

$rubr = "";
for ($k = $ideb; $k <= $ifin; $k++) {
  $ok = "non";
  $i = $indtab[$k];
  if (($titre != "") && ($aut != "")) {//si recherche sur un mot du titre et un auteur
    if ((stripos($titrehref[$i], $titre) !== false) && (stripos($auteursinit[$i], $aut) !== false)){$ok = "oui";}
  }
  if (($titre != "") && ($aut == "")) {//si recherche sur un mot du titre
    if (stripos($titrehref[$i], $titre) !== false){$ok = "oui";}
  }
  if (($titre == "") && ($aut != "")) {//si recherche sur un auteur
    if (stripos($auteursinit[$i], $aut) !== false){$ok = "oui";}
  }
  if (($titre == "") && ($aut == "")) {//aucune recherche sur un titre ou un auteur
    $ok = "oui";
  }
  if ($ok == "oui") { //si la référence est retenue, on continue la routine
    if ($rubr == "") {
      $rubr = $typdoctab[$i];
      if ($halid == "") {
        $text .= "<p class='SousRubrique'><b>".$typdoctab[$i]."</b></p>\r\n";
      }
    }
    if ($rubr != $typdoctab[$i]) {
      if ($halid == "") {
        $text .= "<p class='SousRubrique'><b>".$typdoctab[$i]."</b></p>\r\n";
      }
      $rubr = $typdoctab[$i];
    }
    //mise en évidence des recherches
    $titreaff1 = "<b><u>".$titre."</u></b>";
    $titreaff2 = "<b><u>".ucwords($titre)."</u></b>";
    $titreaff3 = "<b><u>".strtoupper($titre)."</u></b>";
    $titreaff4 = "<b><u>".strtolower($titre)."</u></b>";
    //$autaff1 = "<b><u>".$aut."</u></b>";
		$autaff1 = str_replace(array(".", "-", "'", " ", "(", ")"), array("trolipoint", "trolitiret", "troliapos", "troliesp", "troliparo", "troliparf") , $aut);
		$autaff1 = mise_en_evidence(wd_remove_accents($autaff1), $autaff1, "<b><u>", "</u></b>");
    //$autaff2 = "<b><u>".prenomCompEntier($aut)."</u></b>";
		$autaff2 = str_replace(array(".", "-", "'", " ", "(", ")"), array("trolipoint", "trolitiret", "troliapos", "troliesp", "troliparo", "troliparf") , prenomCompEntier($aut));
		$autaff2 = mise_en_evidence(wd_remove_accents($autaff2), $autaff2, "<b><u>", "</u></b>");
    //$autaff3 = "<b><u>".strtoupper($aut)."</u></b>";
		$autaff3 = str_replace(array(".", "-", "'", " ", "(", ")"), array("trolipoint", "trolitiret", "troliapos", "troliesp", "troliparo", "troliparf") , strtoupper($aut));
		$autaff3 = mise_en_evidence(wd_remove_accents($autaff3), $autaff3, "<b><u>", "</u></b>");
    //$autaff4 = "<b><u>".strtolower($aut)."</u></b>";
		$autaff4 = str_replace(array(".", "-", "'", " ", "(", ")"), array("trolipoint", "trolitiret", "troliapos", "troliesp", "troliparo", "troliparf") , strtolower($aut));
		$autaff4 = mise_en_evidence(wd_remove_accents($autaff4), $autaff4, "<b><u>", "</u></b>");
	
    //si nom composé
    $postiret = strpos($aut,"-");
    $autg = "";
    $autd = "";
    $autgd = "";
    $autaff5 = "";
    if ($postiret != 0) {
      $autg = substr($aut,0,($postiret));
      $autd = substr($aut,($postiret+1),(strlen($aut)-$postiret));
      $autgd = ucfirst($autg)."-".ucfirst($autd);
      //$autaff5 = "<b><u>".$autgd."</u></b>";
			$autaff5 = mise_en_evidence(wd_remove_accents(str_replace(" ", "troliesp", $autgd)), str_replace(" ", "troliesp", $autgd), "<b><u>", "</u></b>");
    }
    //si recherche sur plusieurs auteurs
    $autaff = $auteurs[$i];
		$autaff = str_replace(", ", ",", $autaff);
		//Présence de <i> et al.</i> ? > Si oui, supprimer temporairement pour conserver la mise en évidence du dernier auteur
		$etal = (strpos($autaff, '<i> et al.</i>') !== false) ? 1 : 0;
		if ($etal == 1) {$autaff = substr(str_replace('<i> et al.</i>', '', $autaff), 0, -5);}
		$autfin = "";
    if (isset($_GET['auteur_exp']) && ($_GET['auteur_exp'] != "") || $listenominit2 != "") {
      if (isset($_GET['auteur_exp']) && ($_GET['auteur_exp'] != "")) {
        //$auteur_exp_aff = wd_remove_accents(ucwords($_GET['auteur_exp']));
        $auteur_exp_aff = ucwords($_GET['auteur_exp']);
        $auteur_exp_aff_tab = explode(",", $auteur_exp_aff);
        $ii = 0;
      }else{
        $auteur_exp_aff_tab = explode("~", $listenominit2);
        $ii = 1;
      }
      while (isset($auteur_exp_aff_tab[$ii]) && $auteur_exp_aff_tab[$ii] != "") {
        $autexp0 = str_replace(","," ",$auteur_exp_aff_tab[$ii]);
        //si nom composé
        $postiret = strpos($autexp0,"-");
        if ($postiret != 0) {
          $autg = substr($autexp0,0,($postiret));
          $autd = substr($autexp0,($postiret+1),(strlen($autexp0)-$postiret));
          $autgd0 = ucfirst($autg)."-".ucfirst($autd);
          //$autgd1 = "<b><u>".$autgd0."</u></b>";
					//$autaff = str_replace($autgd0, $autgd1, $autaff);
					$autgd0 = str_replace(array(".", "-", "'", " ", "(", ")"), array("trolipoint", "trolitiret", "troliapos", "troliesp", "troliparo", "troliparf") , $autgd0);
					$autaff = str_replace(array(".", "-", "'", " ", "(", ")"), array("trolipoint", "trolitiret", "troliapos", "troliesp", "troliparo", "troliparf") , $autaff);
					$autaff = mise_en_evidence(wd_remove_accents($autgd0), $autaff, "<b><u>", "</u></b>");
        }
        //$autexp0 = ucwords(strtolower($autexp0));
        //$autexp1 = "<b><u>".$autexp0."</u></b>";
				//$autaff = str_replace($autexp0, $autexp1, $autaff);
				$autexp0 = str_replace(array(".", "-", "'", " ", "(", ")"), array("trolipoint", "trolitiret", "troliapos", "troliesp", "troliparo", "troliparf") , $autexp0);
				$autaff = str_replace(array(".", "-", "'", " ", "(", ")"), array("trolipoint", "trolitiret", "troliapos", "troliesp", "troliparo", "troliparf") , $autaff);
				$autaff = mise_en_evidence(wd_remove_accents($autexp0), $autaff, "<b><u>", "</u></b>");
        $ii += 1;
      }
			if ($etal == 1) {$autaff .= ' <i> et al.</i>';}
    }else{
			$autexp1 = str_replace(array(".", "-", "'", " ", "(", ")"), array("trolipoint", "trolitiret", "troliapos", "troliesp", "troliparo", "troliparf") , $aut);
			$autexp2 = str_replace(array(".", "-", "'", " ", "(", ")"), array("trolipoint", "trolitiret", "troliapos", "troliesp", "troliparo", "troliparf") , prenomCompEntier($aut));
			$autexp3 = str_replace(array(".", "-", "'", " ", "(", ")"), array("trolipoint", "trolitiret", "troliapos", "troliesp", "troliparo", "troliparf") , strtoupper($aut));
			$autexp4 = str_replace(array(".", "-", "'", " ", "(", ")"), array("trolipoint", "trolitiret", "troliapos", "troliesp", "troliparo", "troliparf") , strtolower($aut));
			$autexp5 = str_replace(array(".", "-", "'", " ", "(", ")"), array("trolipoint", "trolitiret", "troliapos", "troliesp", "troliparo", "troliparf") , $autgd);
			$autaff = str_replace(array(".", "-", "'", " ", "(", ")"), array("trolipoint", "trolitiret", "troliapos", "troliesp", "troliparo", "troliparf") , $autaff);
      $autaff = str_replace(array($autexp1, $autexp2, $autexp3, $autexp4, $autexp5),array($autaff1, $autaff2, $autaff3, $autaff4, $autaff5),$auteurs[$i]);
    }
    //si requête avec authIdHal_s
    if ($authidhal != "" & $authidhal_mev != "") {
      //$autaff = str_replace($authidhal_mev, "<b><u>".$authidhal_mev."</u></b>",$auteurs[$i]);
			$authidhal_mev = str_replace(array(".", "-", "'", " ", "(", ")"), array("trolipoint", "trolitiret", "troliapos", "troliesp", "troliparo", "troliparf") , $authidhal_mev);
			$auteurs[$i] = str_replace(array(".", "-", "'", " ", "(", ")"), array("trolipoint", "trolitiret", "troliapos", "troliesp", "troliparo", "troliparf") , $auteurs[$i]);
			$autaff = mise_en_evidence(wd_remove_accents($authidhal_mev), $auteurs[$i], "<b><u>", "</u></b>");
    }
    //corrections
		$autaff = str_replace(array("<b><u><b><u>","</b></u></b></u>","troliesp",","), array("<b><u>","</b></u>"," ",", "), $autaff);
		$autaff = str_replace(array("trolipoint", "trolitiret", "troliapos", "troliesp", "troliparo", "troliparf"), array(".", "-", "'", " ", "(", ")"), $autaff);
    $titreaff = str_replace(array($titre, ucfirst($titre), strtoupper($titre), strtolower($titre)),array($titreaff1, $titreaff2, $titreaff3, $titreaff4),$titrehref[$i]);
    $rvnp[$i] = str_replace(': . ', '', $rvnp[$i]);
    if (isset($_GET['presbib']) && ($_GET['presbib'] != "br")) {
      //$textaff = "<dt class='ChampRes'>Indice</dt><dd class='ValeurRes Indice' style='display: inline; margin-left: 0%; font-size: 1em;'>".$cpt ."&nbsp;-&nbsp;</dd>";
      //$textaff = "<dt class='ChampRes'></dt><dd class='ValeurRes Indice' style='display: inline; margin-left: 0%; font-size: 1em;'>".$cpt ."&nbsp;-&nbsp;</dd>";
      //$textaff .= "<dt class='ChampRes'>Auteurs</dt><dd class='ValeurRes Auteurs' style='display: inline; margin-left: 0%; font-size: 1em;'>".$autaff."</dd>";
      $textaff = "<dt class='ChampRes'></dt><dd class='ValeurRes Auteurs' style='display: inline; margin-left: 0%; font-size: 1em;'>".$cpt ."&nbsp;-&nbsp;".$autaff."</dd>";
      //$textaff .= "<dt class='ChampRes'>Titre</dt><dd class='ValeurRes Titre' style='display: inline; margin-left: 0%; font-size: 1em;'>".$titreaff."</dd>";
      $textaff .= "<dt class='ChampRes'></dt><dd class='ValeurRes Titre' style='display: inline; margin-left: 0%; font-size: 1em;'>".$titreaff."</dd>";
      //$textaff .= "<dt class='ChampRes'>Détail</dt><dd class='ValeurRes Detail' style='display: inline; margin-left: 0%; font-size: 1em;'>".$rvnp[$i]."</dd>";
      $textaff .= "<dt class='ChampRes'></dt><dd class='ValeurRes Detail' style='display: inline; margin-left: 0%; font-size: 1em;'>".$rvnp[$i]."</dd>";
      if ($doi[$i] == "-") {$doiaff = "";}else{$doiaff = $doi[$i];}
      //$textaff .= "<dt class='ChampRes'>DOI</dt><dd class='ValeurRes DOI' style='display: inline; margin-left: 0%; font-size: 1em;'>".$doiaff."</dd>";
      $textaff .= "<dt class='ChampRes'></dt><dd class='ValeurRes DOI' style='display: inline; margin-left: 0%; font-size: 1em;'>".$doiaff."</dd>";
      if ($pubmed[$i] == "-") {$pubmedaff = "";}else{$pubmedaff = $pubmed[$i];}
      $textaff .= "<dt class='ChampRes'></dt><dd class='ValeurRes Pubmed' style='display: inline; margin-left: 0%; font-size: 1em;'>".$pubmedaff."</dd>";
      //$textaff .= "<dt class='ChampRes'>Accès au bibtex</dt><dd class='ValeurRes LienBibtex' style='display: inline; margin-left: 0%; font-size: 1em;'>".$bibtex[$i]."</dd>";
      if ($bt == "oui") {
        $textaff .= "<dt class='ChampRes'></dt><dd class='ValeurRes LienBibtex' style='display: inline; margin-left: 0%; font-size: 1em;'>".$bibtex[$i]."</dd>";
      }else{
        $textaff .= "<dt class='ChampRes'></dt><dd class='ValeurRes LienBibtex' style='display: inline; margin-left: 0%; font-size: 1em;'></dd>";
      }
      $text .= "<dl class='NoticeRes'><div style='margin-left: 3%;'>";
    }else{
      //$textaff = "<dt class='ChampRes'>Indice</dt><dd class='ValeurRes Indice' style='float: left; font-size: 1em;'>".$cpt ."&nbsp;-&nbsp;</dd>";
      $textaff = "<dt class='ChampRes'></dt><dd class='ValeurRes Indice' style='float: left; font-size: 1em;'>".$cpt ."&nbsp;-&nbsp;</dd>";
      //$textaff .= "<dt class='ChampRes'>Auteurs</dt><dd class='ValeurRes Titre' style='font-size: 1em;'>".$titreaff."</dd>";
      $textaff .= "<dt class='ChampRes'></dt><dd class='ValeurRes Titre' style='font-size: 1em;'>".$titreaff."</dd>";
      //$textaff .= "<dt class='ChampRes'>Titre</dt><dd class='ValeurRes Auteurs' style='font-size: 1em;'>".$autaff."</dd>";
      $textaff .= "<dt class='ChampRes'></dt><dd class='ValeurRes Auteurs' style='font-size: 1em;'>".$autaff."</dd>";
      //$textaff .= "<dt class='ChampRes'>Détail</dt><dd class='ValeurRes Detail' style='font-size: 1em;'>".$rvnp[$i]."</dd>";
      $textaff .= "<dt class='ChampRes'></dt><dd class='ValeurRes Detail' style='font-size: 1em;'>".$rvnp[$i]."</dd>";
      if ($doi[$i] == "-") {$doiaff = "";}else{$doiaff = $doi[$i];}
      //$textaff .= "<dt class='ChampRes'>DOI</dt><dd class='ValeurRes DOI' style='font-size: 1em;'>".$doiaff."</dd>";
      $textaff .= "<dt class='ChampRes'></dt><dd class='ValeurRes DOI' style='font-size: 1em;'>".$doiaff."</dd>";
      if ($pubmed[$i] == "-") {$pubmedaff = "";}else{$pubmedaff = $pubmed[$i];}
      $textaff .= "<dt class='ChampRes'></dt><dd class='ValeurRes Pubmed' style='font-size: 1em;'>".$pubmedaff."</dd>";
      //$textaff .= "<dt class='ChampRes'>Accès au bibtex</dt><dd class='ValeurRes' style='display: inline; font-size: 1em;'>".$bibtex[$i]."</dd>";
      if ($bt == "oui") {
        $textaff .= "<dt class='ChampRes'></dt><dd class='ValeurRes' style='display: inline; font-size: 1em;'>".$bibtex[$i]."</dd>";
      }else{
        $textaff .= "<dt class='ChampRes'></dt><dd class='ValeurRes' style='display: inline; font-size: 1em;'></dd>";
      }
      $text .= "<dl class='NoticeRes'>";
    }
    $textaff = str_replace(", &nbsp;-", "&nbsp;-", $textaff);
    $text .= $textaff;

    //export en CSV et RTF
    //Auteurs - titre
    if (isset($_GET['presbib']) && ($_GET['presbib'] != "br")) {
      $chaine = strip_tags(str_replace(";",",",str_replace($presbib,"",$auteurs[$i]))).";";
      $chaine .= strip_tags(str_replace(";",",",str_replace($presbib,"",$titrehref[$i]))).";";
      $sect->writeText($cpt." - ".strip_tags(str_replace($presbib,"",$auteurs[$i])), $font);
      $sect->writeText($presbib, $font);
      $crit = $titrehref[$i];
      $txt1 = strip_tags($crit);
      $txt1 = str_replace($presbib,"",$txt1);
      $txt2 = substr($crit,strpos($crit,"href='")+6,strpos($crit,"'>")-strpos($crit,"href='")-6);
      $sect->writeHyperLink($txt2, $txt1, $fontlien1);
    }else{
      $chaine = strip_tags(str_replace(";",",",str_replace($presbib,"",$titrehref[$i]))).";";
      $chaine .= strip_tags(str_replace(";",",",str_replace($presbib,"",$auteurs[$i]))).";";
      $sect->writeText($cpt." - ", $font);
      $crit = $titrehref[$i];
      $txt1 = strip_tags($crit);
      $txt1 = str_replace($presbib,"",$txt1);
      $txt2 = substr($crit,strpos($crit,"href='")+6,strpos($crit,"'>")-strpos($crit,"href='")-6);
      $sect->writeHyperLink($txt2, $txt1, $fontlien1);
      $sect->writeText($presbib, $font);
      $sect->writeText(strip_tags(str_replace($presbib,"",$auteurs[$i])), $font);
    }
    //Sous-titre
    if ($subtitle[$i] != "-") {
      $chaine .= strip_tags(str_replace(";",",",str_replace($presbib,"",$subtitle[$i]))).";";
    }else{
      $chaine .= ";";
    }
    //RVNP
    //$chaine .= strip_tags(str_replace(";",",",str_replace($presbib,"",$rvnp[$i]))).";";
    $sect->writeText($presbib.str_replace($presbib, "", strip_tags($rvnp[$i])), $font);
    $sect->writeText($presbib, $font);
    //Revue
    if ($journal[$i] != "-") {
      $chaine .= strip_tags(str_replace(";",",",str_replace($presbib,"",$journal[$i]))).";";
    }else{
      $chaine .= ";";
    }
    //Editeur
    if ($mef != 1) {
      if ($journalPublisher[$i] == "-") {$editeur = $scientificEditor[$i];}else{$editeur = $journalPublisher[$i];}
      if ($editeur != "-") {
        $chaine .= strip_tags(str_replace(";",",",str_replace($presbib,"",$editeur))).";";
      }else{
        $chaine .= ";";
      }
    }
    //Année
    if ($prodate[$i] != "-") {
      $chaine .= strip_tags(str_replace(";",",",str_replace($presbib,"",$prodate[$i]))).";";
    }else{
      $chaine .= ";";
    }
    //Volume
    if ($volume[$i] != "-") {
      $chaine .= strip_tags(str_replace(";",",",str_replace($presbib,"",$volume[$i]))).";";
    }else{
      $chaine .= ";";
    }
    //Issue
    if ($mef != 1) {
      if ($issue[$i] != "-") {
        $chaine .= strip_tags(str_replace(";",",",str_replace($presbib,"",$issue[$i]))).";";
      }else{
        $chaine .= ";";
      }
    }
    //Pages
    if ($page[$i] != "-") {
      $chaine .= strip_tags(str_replace(";",",",str_replace($presbib,"",$page[$i]))).";";
    }else{
      $chaine .= ";";
    }
    //DOI
    if ($doi[$i] != "-") {
      $chaine .= strip_tags(str_replace(";",",",str_replace($presbib,"",$doi[$i]))).";";
    }else{
      $chaine .= ";";
    }
    if ($doi[$i] != "-") {
      $crit = $doi[$i];
      $sect->writeText("DOI : ", $font);
      $txt1 = str_replace("DOI : ","",strip_tags($crit));
      $txt1 = str_replace($presbib,"",$txt1);
      $txt2 = substr($crit,strpos($crit,"href='")+6,strpos($crit,"'>")-strpos($crit,"href='")-6);
      $sect->writeHyperLink($txt2, $txt1, $fontlien2);
    }
    //Pubmed
    if ($pubmed[$i] != "-") {
      $chaine .= strip_tags(str_replace(";",",",str_replace($presbib,"",$pubmed[$i]))).";";
    }else{
      $chaine .= ";";
    }
    if ($pubmed[$i] != "-") {
      $crit = $pubmed[$i];
      $sect->writeText($presbib, $font);
      $sect->writeText("Pubmed : ", $font);
      $txt1 = str_replace("Pubmed : ","",strip_tags($crit));
      $txt1 = str_replace($presbib,"",$txt1);
      $txt2 = substr($crit,strpos($crit,"href='")+6,strpos($crit,"'>")-strpos($crit,"href='")-6);
      $sect->writeHyperLink($txt2, $txt1, $fontlien2);
    }
    //Bibtex
    if ($bt == "oui") {
      if ($bibtex[$i] != "-") {
        $chaine .= str_replace(";",",",str_replace($presbib,"",str_replace(array("&nbsp;","target='_blank' "),"",$bibtex[$i])));
      }else{
        $chaine .= ";";
      }
      $sect->writeText($presbib, $font);
      $crit = $bibtex[$i];
      $txt1 = "Bibtex";
      $txt2 = substr($crit,strpos($crit,"href='")+6,strpos($crit,"'>")-strpos($crit,"href='")-6);
      $sect->writeHyperLink($txt2, $txt1, $fontlien2);
    }else{
      $sect->writeText($presbib, $font);
    }

    //PDF
    $j = 1;
    //si plusieurs PDF
    while (isset(${"pdf".$j}[$i])) {
      if (${"pdf".$j}[$i] != "-")  {$text .= ${"pdf".$j}[$i];}
      $j++;
    }
    for($j = 1; $j <= 5; $j++) {
      if (${"pdf".$j}[$i] != "-") {
        $chaine .= ";".str_replace(";",",",str_replace(array("&nbsp;","target='_blank' "),"",${"pdf".$j}[$i]));
      }else{
        $chaine .= ";";
      }
      $crit = ${"pdf".$j}[$i];
      if ($crit != "-") {
        $sect->writeText(" - ", $font);
        $txt1 = "PDF".$j;
        $txt2 = substr($crit,strpos($crit,"href='")+6,strpos($crit,"'>")-strpos($crit,"href='")-6);
        $sect->writeHyperLink($txt2, $txt1, $fontlien2);
      }
    }
    //reprint
    $chaine .= ";".str_replace(";",",",str_replace($presbib,"",str_replace("&nbsp;","",$reprint[$i])));
    $crit = $reprint[$i];
    if ($crit != "&nbsp;") {
      $sect->writeText(" - ", $font);
      $txt1 = "Reprint";
      $txt2 = substr($crit,strpos($crit,"href='")+6,strpos($crit,"'>")-strpos($crit,"href='")-6);
      $sect->writeHyperLink($txt2, $txt1, $fontlien2);
    }
    //Affichage
    if (isset($_GET['presbib']) && ($_GET['presbib'] != "br")) {
      //$text .= "<dt class='ChampRes'>Reprint</dt><dd class='ValeurRes Reprint' style='display: inline; margin-left: 0%;'>".$reprint[$i]."</dd></div></dl>\r\n";
      $text .= "<dd class='ValeurRes Reprint' style='display: inline; margin-left: 0%;'>".$reprint[$i]."</dd></div></dl>\r\n";
    }else{
      //$text .= "<dt class='ChampRes'>Reprint</dt><dd class='ValeurRes Reprint' style='display: inline; margin-left: 0%;'>".$reprint[$i]."</dd></dl>\r\n";
      $text .= "<dd class='ValeurRes Reprint' style='display: inline; margin-left: 0%;'>".$reprint[$i]."</dd></dl>\r\n";
    }
    //export en CSV
    fwrite($inF,$chaine.chr(13).chr(10));

    //export en RTF
    $sect->writeText("<br><br>", $font);
    $rtf->save($Fnm2);
		
		if (isset($_GET['typord']) && ($_GET['typord'] == "asc" || $_GET['typord'] == "descInv")) {$cpt--;}else{$cpt++;}
  }
}

//navigation
if ($halid == "") {
  $text .= "<br><br><div class='navigation'>\r\n";

  $i = 0;
  if ($priorite == "collection_exp") {
    if ($labocrit2 == $collection_exp) {$labocrit2 = "";}
  }else{
    if ($labocrit2 == $labo) {$labocrit2 = "";}
  }
	if (is_numeric($ipas) && is_numeric($irec)) {
		while((($ipas * $i) + 1) <= $irec) {
			$ideb = ($ipas * $i) + 1;
			$ifin = $ideb + $ipas - 1;
			if ($ifin > $irec) {$ifin = $irec;}
			$presbibUrl = "";
			if ($presbib =="<br>") {$presbibUrl = "br";}
			$text .= "<a href=\"?autvar=".$autvar."&labo=".$labo."&collection_exp=".$collection_exp."&equipe_recherche_exp=".$equipe_recherche_exp."&auteur_exp=".$auteur_exp."&mailto=".$mailto."&lang=".$lang."&css=".$css."&form=".$form."&tous=".$tous."&annee_publideb=".$annee_publideb."&anneedep=".$anneedep."&lim_aut=".$lim_aut."&annee_excl=".$annee_excl."&bt=".$bt."&presbib=".$presbibUrl."&labocrit=".$labocrit."&typdoc=".$typdocinit."&anneedeb=".$anneedeb."&anneefin=".$anneefin."&titre=".$titre."&aut=".$aut."&ipas=".$ipas."&typord=".$typord."&ideb=".$ideb."&ifin=".$ifin."&authidhal=".$authidhal."&authidhali=".$authidhali."&authid=".$authid."&notauthid=".$notauthid."&nothal=".$nothal."&lienpubmed=".$lienpubmed."&mef=".$mef."&ids=".$ids."&primary=".$primary."&secondary=".$secondary."&detail=".$detail."&typform=".str_replace(' ', '%20', $typform)."&affDoi=".$affDoi."&affIdh=".$affIdh."&acc=noninit\">".$ideb."-".$ifin."</a>&nbsp;&nbsp;&nbsp;\r\n";
			$i++;
		}
	}
  $text .= "<br><br></div></div>\r\n";
}

fclose($inF);
if ($halid == "") {
  if ($irec != 0) {
    //$text .= "<div style='text-align: center;'><b><a target='_blank' href='http://".$_SERVER['HTTP_HOST']."/HAL/publisHAL.csv'>".$result6."</a></b>\r\n";
    $text .= "<div class='exports'><b><a target='_blank' href='./HAL/publisHAL_".$unicite.".csv'>".$result6."</a></b>\r\n";
    $text .= "  ";
    //$text .= "<b><a target='_blank' href='http://".$_SERVER['HTTP_HOST']."/HAL/publisHAL.rtf'>".$result7."</a></b><br><br></div>\r\n";
    $text .= "<b><a target='_blank' href='./HAL/publisHAL_".$unicite.".rtf'>".$result7."</a></b><br><br></div>\r\n";
  }
}
echo $text;
?>
<br>
</body>
</html>
