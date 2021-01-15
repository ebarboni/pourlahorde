import groovy.json.JsonSlurperClassic 


@NonCPS
def jsonParse(def json) {
    new groovy.json.JsonSlurperClassic().parseText(json)
}

node('setekhmaster') {
   stage('install tools') {

   git 'https://ebarboni@github.com/ebarboni/pourlahorde.git'

   sh "composer install"
}
stage('get data'){
sh 'rm -Rf source/persos/'
sh 'mkdir -p app/_data/'

sh "curl -o app/_data/data.json -X POST 'https://eu.battle.net/oauth/token' -u3492455326544be3b53cdb7dab3eb671:${env.APIKEY} -d grant_type=client_credentials "
sleep (time:50,unit:'MILLISECONDS')
def tk = env.WORKSPACE + '/app/_data/data.json'
def accesstoken = jsonParse(readFile(tk)).get('access_token')

sh 'rm app/_data/data.json'
sh "curl -o app/_data/guildA.json 'https://eu.api.blizzard.com/data/wow/guild/elune/pour-la-horde/roster?namespace=profile-eu&locale=fr_FR&access_token=${accesstoken}'"
sleep (time:50,unit:'MILLISECONDS')
def ga = env.WORKSPACE + '/app/_data/guildA.json'

sh "curl -o app/_data/guildH.json 'https://eu.api.blizzard.com/data/wow/guild/elune/woodoo-awmy/roster?namespace=profile-eu&locale=fr_FR&access_token=${accesstoken}'"
sleep (time:50,unit:'MILLISECONDS')
def gh = env.WORKSPACE + '/app/_data/guildH.json'

sh "curl -o app/_data/achievementguild.json 'https://eu.api.blizzard.com/wow/data/guild/achievements?locale=fr_FR&access_token=${accesstoken}'"
sleep (time:50,unit:'MILLISECONDS')
sh "curl -o app/_data/achievementperso.json 'https://eu.api.blizzard.com/wow/data/character/achievements?locale=fr_FR&access_token=${accesstoken}'"
sleep (time:50,unit:'MILLISECONDS')
def characterA = jsonParse(readFile(ga))
for (charact  in characterA.get('members') ) {
 def n =   charact.get('character').get('name').toString()   
 def m = URLEncoder.encode(n);
sh "curl -o app/_data/${m}.json 'https://eu.api.blizzard.com/wow/character/Elune/${m}?fields=stats,professions,items,statistics,progression,audit,talents,achievements,reputation&locale=fr_FR&access_token=${accesstoken}'"
sleep (time:50,unit:'MILLISECONDS')
}
def characterH = jsonParse(readFile(gh))
for (charact  in characterH.get('members') ) {
  def n =   charact.get('character').get('name').toString()   
 def m = URLEncoder.encode(n);
sh "curl -o app/_data/${m}.json 'https://eu.api.blizzard.com/wow/character/Elune/${m}?fields=stats,professions,items,statistics,progression,audit,talents,achievements,reputation&locale=fr_FR&access_token=${accesstoken}'"
sleep (time:50,unit:'MILLISECONDS')
}
}

stage ('build') {
   sh 'rm -Rf output_prod/'
   sh './vendor/bin/sculpin generate --env=prod'
}
stage ('deploy') {
   sh "cp -r output_prod/ /opt/site/"
}
}
