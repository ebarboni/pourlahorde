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

sh "curl -o app/_data/guildA.json 'https://eu.api.battle.net/wow/guild/Elune/Pour%20la%20horde?fields=achievements%2Cchallenge%2Cmembers%2Cnews&locale=fr_FR&apikey=${env.APIKEY}'"
def ga = env.WORKSPACE + '/app/_data/guildA.json'

sh "curl -o app/_data/guildH.json 'https://eu.api.battle.net/wow/guild/Elune/Woodoo%20Awmy?fields=achievements%2Cchallenge%2Cmembers%2Cnews&locale=fr_FR&apikey=${env.APIKEY}'"
def gh = env.WORKSPACE + '/app/_data/guildH.json'

sh "curl -o app/_data/achievementguild.json 'https://eu.api.battle.net/wow/data/guild/achievements?locale=fr_FR&apikey=${env.APIKEY}'"
sh "curl -o app/_data/achievementperso.json 'https://eu.api.battle.net/wow/data/character/achievements?locale=fr_FR&apikey=${env.APIKEY}'"

def characterA = jsonParse(readFile(ga))
for (charact  in characterA.get('members') ) {
 def n =  charact.get('character').get('name').toString()
sh "curl -o app/_data/${n}.json 'https://eu.api.battle.net/wow/character/Elune/${n}?fields=stats,professions,items,statistics,progression,audit,talents,achievements,reputation&locale=fr_FR&apikey=${env.APIKEY}'"
sleep 1
}
def characterH = jsonParse(readFile(gh))
for (charact  in characterH.get('members') ) {
 def n =  charact.get('character').get('name').toString()
sh "curl -o app/_data/${n}.json 'https://eu.api.battle.net/wow/character/Elune/${n}?fields=professions,items,statistics,progression,audit,talents,achievements,reputation&locale=fr_FR&apikey=${env.APIKEY}'"
sleep 1
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