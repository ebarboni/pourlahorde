import groovy.json.JsonSlurperClassic 


@NonCPS
def jsonParse(def json) {
    new groovy.json.JsonSlurperClassic().parseText(json)
}

node('setekhmaseter') {
   stage('install tools') {

   git 'https://ebarboni@github.com/ebarboni/pourlahorde.git'

   sh "composer install"
}
stage('get data'){

sh 'mkdir -p app/_data/'

sh "curl -o app/_data/data.json -X POST 'https://eu.battle.net/oauth/token' -u3492455326544be3b53cdb7dab3eb671:${env.APIKEY} -d grant_type=client_credentials "
def tk = env.WORKSPACE + '/app/_data/data.json'
def accesstoken = jsonParse(readFile(tk)).get('access_token')
sh 'rm app/_data/data.json'
sh "curl -o app/_data/guildA.json 'https://eu.api.blizzard.com/wow/guild/Elune/Pour%20la%20horde?fields=achievements%2Cchallenge%2Cmembers%2Cnews&locale=fr_FR&access_token=${accesstoken}'"
def ga = env.WORKSPACE + '/app/_data/guildA.json'

sh "curl -o app/_data/guildH.json 'https://eu.api.blizzard.com/wow/guild/Elune/Woodoo%20Awmy?fields=achievements%2Cchallenge%2Cmembers%2Cnews&locale=fr_FR&access_token=${accesstoken}'"
def gh = env.WORKSPACE + '/app/_data/guildH.json'

sh "curl -o app/_data/achievementguild.json 'https://eu.api.blizzard.com/wow/data/guild/achievements?locale=fr_FR&access_token=${accesstoken}'"
sh "curl -o app/_data/achievementperso.json 'https://eu.api.blizzard.com/wow/data/character/achievements?locale=fr_FR&access_token=${accesstoken}'"

def characterA = jsonParse(readFile(ga))
for (charact  in characterA.get('members') ) {
 def n =  new String(charact.get('character').get('name').toString().getBytes(),"UTF-8")
sh "curl -o app/_data/${n}.json 'https://eu.api.blizzard.com/wow/character/Elune/${n}?fields=stats,professions,items,statistics,progression,audit,talents,achievements,reputation&locale=fr_FR&access_token=${accesstoken}'"
sleep (time:50,unit:'MILLISECONDS')
}
def characterH = jsonParse(readFile(gh))
for (charact  in characterH.get('members') ) {
 def n =  new String(charact.get('character').get('name').toString().getBytes(),"UTF-8")
sh "curl -o app/_data/${n}.json 'https://eu.api.blizzard.com/wow/character/Elune/${n}?fields=stats,professions,items,statistics,progression,audit,talents,achievements,reputation&locale=fr_FR&access_token=${accesstoken}'"
sleep (time:50,unit:'MILLISECONDS')
}
}

stage ('build') {
   sh 'rm -Rf output_prod/'
   sh './vendor/bin/sculpin generate --env=prod'
}
stage ('deploy') {
   sh "tar -zcvf /tmp/site.tar.gz output_prod/"
}
}