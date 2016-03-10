<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"  
	xmlns:data="http://example.com/2006/some-data"
	xmlns:set="http://exslt.org/sets"
	xmlns:msxsl="urn:schemas-microsoft-com:xslt" 
	exclude-result-prefixes="msxsl"
	xmlns:exsl="http://exslt.org/common"
>

<xsl:output method='html' />

<xsl:template match='text()' />

<xsl:template match="students">
  <xsl:apply-templates select="transport"/>
</xsl:template>

<xsl:template match="transport">
  <xsl:variable name="busname">
	<xsl:value-of select="name/value/text()" />
  </xsl:variable>


  <div id='logo'>
	<img src="../images/schoollogo.png" height="30px"/>
  </div>

  <div class="right">
	<table>
	  <tr>
		<th>
		  <label>
			Transport
		  </label>
		  <xsl:value-of select="$busname" />&#160;
		</th>
	  </tr>
	  <tr>	
		<th>
			<label>
			  <xsl:value-of select="day/value/text()" />&#160;
			  <xsl:value-of select="date/value/text()" />&#160;
			</label>
			Morning
		</th>
	  </tr>
	</table>
  </div>

  <div class="spacer">
  </div>
	<xsl:variable name="ins" select="set:distinct(student[journey[bus/value=$busname]/direction='I' or (not(journey[bus/value=$busname]/direction='I') and otherjourney[bus/value=$busname]/direction='I')])" />


	<xsl:variable name="stdsin">
	  <xsl:for-each select="$ins">
	    <xsl:sort select="journey[(bus/value=$busname and direction='I') or (not(bus/value=$busname and direction='I') and ../otherjourney[bus/value=$busname]/direction='I')]/stop/sequence" data-type="number" order="ascending" />
	    <xsl:copy-of select="." />
	  </xsl:for-each>
	</xsl:variable>
	
		<xsl:apply-templates select="set:distinct(student/journey[bus/value=$busname][direction='I']/stop|student/otherjourney[bus/value=$busname][direction='I']/stop)">
		  <xsl:sort select="sequence" data-type="number" order="ascending"/>
		  <!--xsl:with-param name="students" select="set:distinct(student[journey[bus/value=$busname]/direction='I' or (not(journey[bus/value=$busname]/direction='I') and otherjourney[bus/value=$busname]/direction='I')])"/-->
		  <xsl:with-param name="students" select="$stdsin" />
		  <xsl:with-param name="busname" select="$busname"/>
		  <xsl:with-param name="direction">I</xsl:with-param>
		  <xsl:with-param name="sidno">0</xsl:with-param>
		</xsl:apply-templates>


	<div class="spacer">
	</div>
	<div class="center">
	  <table class="fullwidth grades">
		<tr>
		  <th style="width:100%;">
			<xsl:text>Observations</xsl:text>
			<xsl:apply-templates select="student/journey[direction='I']/comment[value!='']" />
		  </th>
		</tr>
	  </table>
	</div>

	<div class="spacer">
	</div>
	<hr />
	<div class="spacer">
	</div>


	<div id='logo'>
	  <img src="../images/schoollogo.png" height="30px"/>
	</div>

	<div class="right">
	  <table>
		<tr>
		  <th>
			<label>
			  Transport
			</label>
			<xsl:value-of select="$busname" />&#160;
		  </th>
		</tr>
		<tr>	
		  <th>
			<label>
			  <xsl:value-of select="day/value/text()" />&#160;
			  <xsl:value-of select="date/value/text()" />&#160;
			</label>
			Afternoon
		  </th>
		</tr>
	  </table>
	</div>



	<div class="spacer">
	</div>

	<xsl:variable name="outs" select="set:distinct(student[journey[bus/value=$busname]/direction='O' or (not(journey[bus/value=$busname]/direction='O') and otherjourney[bus/value=$busname]/direction='O')])" />

	<xsl:variable name="stdsout">
	  <xsl:for-each select="$outs">
	    <xsl:sort select="journey[(bus/value=$busname and direction='O') or (not(bus/value=$busname and direction='O') and ../otherjourney[bus/value=$busname]/direction='O')]/stop/sequence" data-type="number" order="ascending" />
	    <xsl:copy-of select="." />
	  </xsl:for-each>
	</xsl:variable>

	<xsl:apply-templates select="set:distinct(student/journey[bus/value=$busname][direction='O']/stop|student/otherjourney[bus/value=$busname][direction='O']/stop)">
	  <xsl:sort select="sequence" data-type="number" order="ascending"/>
	  <!--xsl:with-param name="students" select="set:distinct(student[journey[bus/value=$busname]/direction='O' or (not(journey[bus/value=$busname]/direction='O') and otherjourney[bus/value=$busname]/direction='O')])"/-->
	  <xsl:with-param name="students" select="$stdsout"/>
	  <xsl:with-param name="busname" select="$busname"/>
	  <xsl:with-param name="direction">O</xsl:with-param>
	  <xsl:with-param name="sidno">0</xsl:with-param>
	</xsl:apply-templates>


	<div class="spacer">
	</div>
	<div class="center">
	  <table class="fullwidth grades">
		<tr>
		  <th style="width:100%;">
			<xsl:text>Observations</xsl:text>
			<xsl:apply-templates select="student/journey[direction='O']/comment[value!='']" />
		  </th>
		</tr>
	  </table>
	</div>


	<xsl:if test="position()!=last()">
	  <div class="spacer">
	  </div>
	  <hr />
	  <div class="spacer">
	  </div>
	</xsl:if>


</xsl:template>






<xsl:template match="stop">
  <xsl:param name="students"></xsl:param>
  <xsl:param name="busname"></xsl:param>
  <xsl:param name="direction"></xsl:param>
  <xsl:param name="sidno"></xsl:param>

  <xsl:variable name="stopname">
	<xsl:value-of select="value" />
  </xsl:variable>

	<table class="fullwidth grades">
	  <tr>
		<th colspan="100">
		  <xsl:value-of select="sequence" />
		  <xsl:text>-</xsl:text>
		  <xsl:value-of select="$stopname" />
		  <div style="float:right;"><xsl:value-of select="departuretime" /></div>
		</th>
	  </tr>
	  <tr>
		<th style="width:2%;">
		</th>
		<th colspan="2" style="width:25%;">
		</th>
		<th colspan="5">
		<xsl:text></xsl:text>
		</th>
		<th colspan="5">
		  <xsl:text></xsl:text>
		</th>
		<th colspan="5">
		  <xsl:text></xsl:text>
		</th>
		<th colspan="5">
		  <xsl:text></xsl:text>
		</th>
	  </tr>

			<xsl:for-each select="exsl:node-set($students)/student[journey[bus/value=$busname][direction=$direction] or (not(journey[bus/value=$busname][direction=$direction]) and otherjourney[bus/value=$busname][direction=$direction])]" >
			  <xsl:variable name="i" select="position()" />
			  <xsl:if test="journey[bus/value=$busname][direction=$direction]/stop/value=$stopname or (not(journey[bus/value=$busname][direction=$direction]/stop/value=$stopname) and otherjourney[bus/value=$busname][direction=$direction]/stop/value=$stopname)" >
			    <xsl:call-template name="studentrow">
				<xsl:with-param name="student" select="."/>
				<xsl:with-param name="sidno" select="$i"/>
			    </xsl:call-template>
			  </xsl:if>
			</xsl:for-each>

  </table>

  
</xsl:template>







<xsl:template name="studentrow">
  <xsl:param name="student"></xsl:param>
  <xsl:param name="sidno"></xsl:param>
  <tr>
	<th>
	  <xsl:value-of select="$sidno" />
	</th>
	<th>
	  <xsl:value-of select="./displayfullname/value/text()" />&#160;
	</th>
	<th>
	  <xsl:value-of select="./tutorgroup/value/text()" />
	</th>
	<td>
	  <xsl:text>&#160;</xsl:text>
	</td>
	<td>
	  <xsl:text>&#160;</xsl:text>
	</td>
	<td>
	  <xsl:text>&#160;</xsl:text>
	</td>
	<td>
	  <xsl:text>&#160;</xsl:text>
	</td>
	<td>
	  <xsl:text>&#160;</xsl:text>
	</td>
	<td>
	  <xsl:text>&#160;</xsl:text>
	</td>
	<td>
	  <xsl:text>&#160;</xsl:text>
	</td>
	<td>
	  <xsl:text>&#160;</xsl:text>
	</td>
	<td>
	  <xsl:text>&#160;</xsl:text>
	</td>
	<td>
	  <xsl:text>&#160;</xsl:text>
	</td>
	<td>
	  <xsl:text>&#160;</xsl:text>
	</td>
	<td>
	  <xsl:text>&#160;</xsl:text>
	</td>
	<td>
	  <xsl:text>&#160;</xsl:text>
	</td>
	<td>
	  <xsl:text>&#160;</xsl:text>
	</td>
	<td>
	  <xsl:text>&#160;</xsl:text>
	</td>
	<td>
	  <xsl:text>&#160;</xsl:text>
	</td>
	<td>
	  <xsl:text>&#160;</xsl:text>
	</td>
	<td>
	  <xsl:text>&#160;</xsl:text>
	</td>
	<td>
	  <xsl:text>&#160;</xsl:text>
	</td>
	<td>
	  <xsl:text>&#160;</xsl:text>
	</td>
  </tr>
</xsl:template>



<xsl:template match="comment">
  <p><xsl:value-of select="value/text()" /></p>
</xsl:template>


</xsl:stylesheet>
